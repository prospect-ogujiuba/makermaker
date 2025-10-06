<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\DeliveryMethod;
use MakerMaker\Http\Fields\DeliveryMethodFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class DeliveryMethodController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('delivery_methods.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(DeliveryMethod::class)->useErrors()->useOld()->useConfirm();
        return View::new('delivery_methods.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(DeliveryMethodFields $fields, DeliveryMethod $delivery_method, Response $response, AuthUser $user)
    {
        if (!$delivery_method->can('create')) {
            $response->unauthorized('Unauthorized: Delivery Method not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $delivery_method->created_by = $user->ID;
        $delivery_method->updated_by = $user->ID;

        $delivery_method->save($fields);

        return tr_redirect()->toPage('deliverymethod', 'index')
            ->withFlash('Delivery Method created');
    }

    /**
     * The edit page for admin
     *
     * @param DeliveryMethod $delivery_method
     *
     * @return mixed
     */
    public function edit(DeliveryMethod $delivery_method, AuthUser $user)
    {
        $current_id = $delivery_method->getID();
        $services = $delivery_method->services;
        $createdBy = $delivery_method->createdBy;
        $updatedBy = $delivery_method->updatedBy;

        $form = tr_form($delivery_method)->useErrors()->useOld()->useConfirm();
        return View::new('delivery_methods.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param DeliveryMethod $delivery_method
     *
     * @return mixed
     */
    public function update(DeliveryMethod $delivery_method, DeliveryMethodFields $fields, Response $response, AuthUser $user)
    {
        if (!$delivery_method->can('update')) {
            $response->unauthorized('Unauthorized: Delivery Method not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);


        $delivery_method->updated_by = $user->ID;

        $delivery_method->save($fields);

        return tr_redirect()->toPage('deliverymethod', 'edit', $delivery_method->getID())
            ->withFlash('Delivery Method updated');
    }

    /**
     * The show page for admin
     *
     * @param DeliveryMethod $delivery_method
     *
     * @return mixed
     */
    public function show(DeliveryMethod $delivery_method)
    {
        return $delivery_method;
    }

    /**
     * The delete page for admin
     *
     * @param DeliveryMethod $delivery_method
     *
     * @return mixed
     */
    public function delete(DeliveryMethod $delivery_method)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param DeliveryMethod $delivery_method
     *
     * @return mixed
     */
    public function destroy(DeliveryMethod $delivery_method, Response $response)
    {
        if (!$delivery_method->can('destroy')) {
            return $response->unauthorized('Unauthorized: Delivery Method not deleted');
        }

        $service_count = $delivery_method->services()->count('service_id');

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this delivery method.")
                ->setStatus(409)
                ->setData('delivery_method', $delivery_method);
        }

        $deleted = $delivery_method->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Delivery Method deleted.')->setData('delivery_method', $delivery_method);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $delivery_methods = DeliveryMethod::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($delivery_methods)) {
                return $response
                    ->setData('delivery_methods', [])
                    ->setMessage('No Service Delivery Methods found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('delivery_methods', $delivery_methods)
                ->setMessage('Service Delivery Methods retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Delivery Method indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Delivery Methods: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param DeliveryMethod $delivery_method
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(DeliveryMethod $delivery_method, Response $response)
    {
        try {
            $delivery_method = $delivery_method->with(['services', 'createdBy', 'updatedBy'])->first();

            if (!$delivery_method) {
                return $response
                    ->setMessage('Delivery Method not found', 'error')
                    ->setStatus(404);
            }

            return $response
                ->setData('delivery_method', $delivery_method)
                ->setMessage('Delivery Method retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Delivery Method showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving the Service Delivery Method', 'error')
                ->setStatus(500);
        }
    }
}
