<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceAttributeDefinitionFields;
use MakerMaker\Models\ServiceAttributeDefinition;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceAttributeDefinitionController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_attribute_definitions.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceAttributeDefinition::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_attribute_definitions.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceAttributeDefinitionFields $fields, ServiceAttributeDefinition $service_attribute_definition, Response $response, AuthUser $user)
    {
        if (!$service_attribute_definition->can('create')) {
            $response->unauthorized('Unauthorized: Service Attribute Definition not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'label', '_');

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_attribute_definition->save($fields);

        return tr_redirect()->toPage('serviceattributedefinition', 'index')
            ->withFlash('Service Attribute Definition created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function edit(ServiceAttributeDefinition $service_attribute_definition, AuthUser $user)
    {
        $current_id = $service_attribute_definition->getID();
        $services = $service_attribute_definition->services;
        $attribute_values = $service_attribute_definition->attributeValues;
        $createdBy = $service_attribute_definition->createdBy;
        $updatedBy = $service_attribute_definition->updatedBy;

        $form = tr_form($service_attribute_definition)->useErrors()->useOld()->useConfirm();
        return View::new('service_attribute_definitions.form', compact('form', 'current_id', 'services', 'attribute_values', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function update(ServiceAttributeDefinition $service_attribute_definition, ServiceAttributeDefinitionFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_attribute_definition->can('update')) {
            $response->unauthorized('Unauthorized: ServiceAttributeDefinition not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'label', '_');
        if (!$fields['enum_options']) {
            $service_attribute_definition->enum_options = 'hey';
        }

        $fields['updated_by'] = $user->ID;
        $service_attribute_definition->save($fields);

        return tr_redirect()->toPage('serviceattributedefinition', 'edit', $service_attribute_definition->getID())
            ->withFlash('Service Attribute Definition updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function show(ServiceAttributeDefinition $service_attribute_definition)
    {
        return $service_attribute_definition->with(['services', 'serviceType', 'attributeValues', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function delete(ServiceAttributeDefinition $service_attribute_definition)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function destroy(ServiceAttributeDefinition $service_attribute_definition, Response $response)
    {
        if (!$service_attribute_definition->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Attribute Definition not deleted');
        }

        $service_count = $service_attribute_definition->serviceType()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} Service Type(s) still use this.")
                ->setStatus(409)
                ->setData('service_attribute_definition', $service_attribute_definition);
        }

        $deleted = $service_attribute_definition->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Attribute Definition deleted.')->setData('service_attribute_definition', $service_attribute_definition);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_attribute_definitions = ServiceAttributeDefinition::new()
                ->with(['services', 'serviceType', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_attribute_definitions)) {
                return $response
                    ->setData('service_attribute_definitions', [])
                    ->setMessage('No service Attribute Definitions found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definitions', $service_attribute_definitions)
                ->setMessage('Service Attribute Definition retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Attribute Definition indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Attribute Definitions: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceAttributeDefinition $service_attribute_definition
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceAttributeDefinition $service_attribute_definition, Response $response)
    {
        try {
            $service_attribute_definition = ServiceAttributeDefinition::new()
                ->with(['services', 'serviceType', 'createdBy', 'updatedBy'])
                ->find($service_attribute_definition->getID());

            if (empty($service_attribute_definition)) {
                return $response
                    ->setData('service_attribute_definition', null)
                    ->setMessage('Service Attribute Definition not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_attribute_definition', $service_attribute_definition)
                ->setMessage('Service Attribute Definition retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Attribute Definition showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Attribute Definition', 'error')
                ->setStatus(500);
        }
    }
}
