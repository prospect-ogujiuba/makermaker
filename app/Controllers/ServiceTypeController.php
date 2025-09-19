<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceTypeFields;
use MakerMaker\Models\ServiceType;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceTypeController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_types.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceType::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_types.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceTypeFields $fields, ServiceType $service_type, Response $response, AuthUser $user)
    {
        if (!$service_type->can('create')) {
            $response->unauthorized('Unauthorized: ServiceType not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name', true);

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_type->save($fields);

        return tr_redirect()->toPage('servicetype', 'index')
            ->withFlash('Service Type created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceType $service_type
     *
     * @return mixed
     */
    public function edit(ServiceType $service_type, AuthUser $user)
    {
        $current_id = $service_type->getID();
        $services = $service_type->services;
        $attributeDefinitions = $service_type->attributeDefinitions;
        $createdBy = $service_type->createdBy;
        $updatedBy = $service_type->updatedBy;

        $form = tr_form($service_type)->useErrors()->useOld()->useConfirm();
        return View::new('service_types.form', compact('form', 'current_id', 'services', 'attributeDefinitions', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceType $service_type
     *
     * @return mixed
     */
    public function update(ServiceType $service_type, ServiceTypeFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_type->can('update')) {
            $response->unauthorized('Unauthorized: ServiceType not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name', true);

        $fields['updated_by'] = $user->ID;

        $service_type->save($fields);

        return tr_redirect()->toPage('servicetype', 'edit', $service_type->getID())
            ->withFlash('Service Type updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceType $service_type
     *
     * @return mixed
     */
    public function show(ServiceType $service_type)
    {
        return $service_type->with(['services', 'attributeDefinitions', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceType $service_type
     *
     * @return mixed
     */
    public function delete(ServiceType $service_type)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceType $service_type
     *
     * @return mixed
     */
    public function destroy(ServiceType $service_type, Response $response)
    {
        if (!$service_type->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceType not deleted');
        }

        $service_count = $service_type->services()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service type(s) still use this.")
                ->setStatus(409);
        }

        $deleted = $service_type->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Type deleted.')->setData('service_pricing_model', $service_type);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_types = ServiceType::new()
                ->with(['services', 'attributeDefinitions', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_types)) {
                return $response
                    ->setData('service_types', [])
                    ->setMessage('No Service Types found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_types', $service_types)
                ->setMessage('Service Types retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Type indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Types: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

        /**
     * The show function for API
     *
     * @param ServiceType $service_type
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceType $service_type, Response $response)
    {
        try {
            $service_type = ServiceType::new()
                ->with(['services', 'attributeDefinitions', 'createdBy', 'updatedBy'])
                ->find($service_type->getID());

            if (empty($service_type)) {
                return $response
                    ->setData('service_type', null)
                    ->setMessage('Service Type not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_type', $service_type)
                ->setMessage('Service Type retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Type showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Type', 'error')
                ->setStatus(500);
        }
    }
}
