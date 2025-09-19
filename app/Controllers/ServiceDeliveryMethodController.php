<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceDeliveryMethod;
use MakerMaker\Http\Fields\ServiceDeliveryMethodFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class ServiceDeliveryMethodController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_delivery_methods.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceDeliveryMethod::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_delivery_methods.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceDeliveryMethodFields $fields, ServiceDeliveryMethod $service_delivery_method, Response $response, AuthUser $user)
    {
        if (!$service_delivery_method->can('create')) {
            $response->unauthorized('Unauthorized: Service Delivery Method not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_delivery_method->save($fields);

        return tr_redirect()->toPage('servicedeliverymethod', 'index')
            ->withFlash('Service Delivery Method created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function edit(ServiceDeliveryMethod $service_delivery_method, AuthUser $user)
    {
        $current_id = $service_delivery_method->getID();
        $services = $service_delivery_method->services;
        $createdBy = $service_delivery_method->createdBy;
        $updatedBy = $service_delivery_method->updatedBy;

        $form = tr_form($service_delivery_method)->useErrors()->useOld()->useConfirm();
        return View::new('service_delivery_methods.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function update(ServiceDeliveryMethod $service_delivery_method, ServiceDeliveryMethodFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_delivery_method->can('update')) {
            $response->unauthorized('Unauthorized: Service Delivery Method not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');

        $fields['updated_by'] = $user->ID;

        $service_delivery_method->save($fields);

        return tr_redirect()->toPage('servicedeliverymethod', 'edit', $service_delivery_method->getID())
            ->withFlash('Service Delivery Method updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function show(ServiceDeliveryMethod $service_delivery_method)
    {
        return $service_delivery_method->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function delete(ServiceDeliveryMethod $service_delivery_method)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function destroy(ServiceDeliveryMethod $service_delivery_method, Response $response)
    {
        if (!$service_delivery_method->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Delivery Method not deleted');
        }

        $service_count = $service_delivery_method->services()->count('service_id');

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this delivery method.")
                ->setStatus(409);
        }

        $deleted = $service_delivery_method->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Delivery Method deleted.')->setData('service_delivery_method', $service_delivery_method);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_delivery_methods = ServiceDeliveryMethod::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_delivery_methods)) {
                return $response
                    ->setData('service_delivery_methods', [])
                    ->setMessage('No Service Delivery Methods found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_delivery_methods', $service_delivery_methods)
                ->setMessage('Service Delivery Methods retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivery Method indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Delivery Methods: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceDeliveryMethod $service_delivery_method
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceDeliveryMethod $service_delivery_method, Response $response)
    {
        try {
            $service_delivery_method = $service_delivery_method->with(['services', 'createdBy', 'updatedBy'])->first();

            if (!$service_delivery_method) {
                return $response
                    ->setMessage('Service Delivery Method not found', 'error')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_delivery_method', $service_delivery_method)
                ->setMessage('Service Delivery Method retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivery Method showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving the Service Delivery Method', 'error')
                ->setStatus(500);
        }
    }
}
