<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceDeliveryFields;
use MakerMaker\Models\ServiceDelivery;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceDeliveryController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_delivery.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceDelivery::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_delivery.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceDeliveryFields $fields, ServiceDelivery $service_delivery, Response $response, AuthUser $user)
    {
        if (!$service_delivery->can('create')) {
            $response->unauthorized('Unauthorized: Service Delivery not created')->abort();
        }

        $service_delivery->created_by = $user->ID;
        $service_delivery->updated_by = $user->ID;

        $service_delivery->save($fields);

        return tr_redirect()->toPage('servicedelivery', 'index')
            ->withFlash('Service Delivery created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceDelivery $service_delivery
     *
     * @return mixed
     */
    public function edit(ServiceDelivery $service_delivery, AuthUser $user)
    {
        $current_id = $service_delivery->getID();
        $createdBy = $service_delivery->createdBy;
        $updatedBy = $service_delivery->updatedBy;

        $form = tr_form($service_delivery)->useErrors()->useOld()->useConfirm();
        return View::new('service_delivery.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDelivery $service_delivery
     *
     * @return mixed
     */
    public function update(ServiceDelivery $service_delivery, ServiceDeliveryFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_delivery->can('update')) {
            $response->unauthorized('Unauthorized: Service Delivery not updated')->abort();
        }

        $service_delivery->updated_by = $user->ID;

        $service_delivery->save($fields);

        return tr_redirect()->toPage('servicedelivery', 'edit', $service_delivery->getID())
            ->withFlash('Service Delivery updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceDelivery $service_delivery
     *
     * @return mixed
     */
    public function show(ServiceDelivery $service_delivery)
    {
        return $service_delivery;
    }

    /**
     * The delete page for admin
     *
     * @param ServiceDelivery $service_delivery
     *
     * @return mixed
     */
    public function delete(ServiceDelivery $service_delivery)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDelivery $service_delivery
     *
     * @return mixed
     */
    public function destroy(ServiceDelivery $service_delivery, Response $response)
    {
        if (!$service_delivery->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Delivery not deleted');
        }

        $deleted = $service_delivery->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Delivery deleted.')->setData('service_pricing_model', $service_delivery);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_delivery = ServiceDelivery::new()->get();

            if (empty($service_delivery)) {
                return $response
                    ->setData('service_delivery', [])
                    ->setMessage('No Service Delivery found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_delivery', $service_delivery)
                ->setMessage('Service Delivery retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivery indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Delivery: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceDelivery $service_delivery
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceDelivery $service_delivery, Response $response)
    {
        try {
            $service_delivery = ServiceDelivery::new()
                ->find($service_delivery->getID());

            if (empty($service_delivery)) {
                return $response
                    ->setData('service_delivery', null)
                    ->setMessage('Service Delivery not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_delivery', $service_delivery)
                ->setMessage('Service Delivery retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivery showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Delivery', 'error')
                ->setStatus(500);
        }
    }
}
