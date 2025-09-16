<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceBundleItem;
use MakerMaker\Http\Fields\ServiceBundleItemFields;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceBundleItemController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_bundle_items.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceBundleItem::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_bundle_items.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceBundleItemFields $fields, ServiceBundleItem $service_bundle_item, Response $response, AuthUser $user)
    {
        if (!$service_bundle_item->can('create')) {
            $response->unauthorized('Unauthorized: Service Delivery_assignment not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_bundle_item->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'index')
            ->withFlash('Service Delivery_assignment Created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function edit(ServiceBundleItem $service_bundle_item, AuthUser $user)
    {
        $current_id = $service_bundle_item->getID();
        $services = $service_bundle_item->bundle->services;
        $createdBy = $service_bundle_item->createdBy;
        $updatedBy = $service_bundle_item->updatedBy;

        $form = tr_form($service_bundle_item)->useErrors()->useOld()->useConfirm();
        return View::new('service_bundle_items.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function update(ServiceBundleItem $service_bundle_item, ServiceBundleItemFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_bundle_item->can('update')) {
            $response->unauthorized('Unauthorized: ServiceBundleItem not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_bundle_item->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'edit', $service_bundle_item->getID())
            ->withFlash('Service Delivery_assignment Updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function show(ServiceBundleItem $service_bundle_item)
    {
        return $service_bundle_item->with(['service', 'addonService', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function delete(ServiceBundleItem $service_bundle_item)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function destroy(ServiceBundleItem $service_bundle_item, Response $response)
    {
        if (!$service_bundle_item->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceBundleItem not deleted');
        }

        $servicesCount = $service_bundle_item->service()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} Service Relationship(s) still use this. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_bundle_item->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Relationship deleted.')->setData('service_pricing_model', $service_bundle_item);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceDelServiceBundleItem = ServiceBundleItem::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDelServiceBundleItem)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No service Delivery_assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceDelServiceBundleItem)
                ->setMessage('Service Delivery_assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceBundleItem indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Relationship: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
