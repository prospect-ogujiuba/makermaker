<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceAttributeValueFields;
use MakerMaker\Models\ServiceAttributeDefinition;
use MakerMaker\Models\ServiceAttributeValue;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceAttributeValueController extends Controller
{
    /**
     * Build attribute definition options and JS data
     * 
     * @return array
     */
    private function buildAttributeDefinitionData()
    {
        // Get ALL attribute definitions with service type info
        $attributeDefinitions = ServiceAttributeDefinition::new()->with(['serviceType'])->findAll()->get();
        $options = ['Select Attribute Definition' => null];
        $jsData = [];

        foreach ($attributeDefinitions as $def) {
            $unit = $def->unit ? " {$def->unit}" : '';
            $displayText = "{$def->label} - {$unit} : ({$def->data_type})";
            $options[$displayText] = $def->id;

            // Build JS data for filtering
            $jsData[$def->id] = [
                'service_type_id' => $def->service_type_id,
                'text' => $displayText
            ];
        }

        return compact('options', 'jsData');
    }

    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_attribute_values.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {

        $attributeData = $this->buildAttributeDefinitionData();
        $form = tr_form(ServiceAttributeValue::class)->useErrors()->useOld()->useConfirm();

        return View::new('service_attribute_values.form', array_merge([
            'form' => $form,
            'user' => $user
        ], $attributeData));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceAttributeValueFields $fields, ServiceAttributeValue $service_attribute_value, Response $response, AuthUser $user)
    {
        if (!$service_attribute_value->can('create')) {
            $response->unauthorized('Unauthorized: Service Attribute Value not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_attribute_value->save($fields);

        return tr_redirect()->toPage('serviceattributevalue', 'index')
            ->withFlash('Service Attribute Value created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function edit(ServiceAttributeValue $service_attribute_value, AuthUser $user)
    {
        $current_id = $service_attribute_value->getID();
        $service = $service_attribute_value->service;
        $createdBy = $service_attribute_value->createdBy;
        $updatedBy = $service_attribute_value->updatedBy;

        $attributeData = $this->buildAttributeDefinitionData();

        $form = tr_form($service_attribute_value)->useErrors()->useOld()->useConfirm();
        return View::new('service_attribute_values.form', array_merge([
            'form' => $form,
            'current_id' => $current_id,
            'service' => $service,
            'createdBy' => $createdBy,
            'updatedBy' => $updatedBy,
            'user' => $user
        ], $attributeData));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function update(ServiceAttributeValue $service_attribute_value, ServiceAttributeValueFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_attribute_value->can('update')) {
            $response->unauthorized('Unauthorized: ServiceAttributeValue not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_attribute_value->save($fields);

        return tr_redirect()->toPage('serviceattributevalue', 'edit', $service_attribute_value->getID())
            ->withFlash('Service Attribute Value updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function show(ServiceAttributeValue $service_attribute_value)
    {
        return $service_attribute_value->with(['service', 'attributeDefinition', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function delete(ServiceAttributeValue $service_attribute_value)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function destroy(ServiceAttributeValue $service_attribute_value, Response $response)
    {
        if (!$service_attribute_value->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceAttributeValue not deleted');
        }

        $service_count = $service_attribute_value->service()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service Attribute Value(s) still use this.")
                ->setStatus(409)
                ->setData('service_attribute_value', $service_attribute_value);
        }

        $deleted = $service_attribute_value->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Attribute Value deleted.')->setData('service_attribute_value', $service_attribute_value);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceAttributeValue = ServiceAttributeValue::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceAttributeValue)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No service Attribute Values found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceAttributeValue)
                ->setMessage('Service Attribute Value retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceAttributeValue indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve service Attribute Value: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
