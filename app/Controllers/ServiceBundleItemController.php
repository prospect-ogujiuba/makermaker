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
            $response->unauthorized('Unauthorized: Service Bundle Item not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_bundle_item->save($fields);

        return tr_redirect()->toPage('servicebundleitem', 'index')
            ->withFlash('Service Bundle Item created');
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
            $response->unauthorized('Unauthorized: Service Bundle Item not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_bundle_item->save($fields);

        return tr_redirect()->toPage('servicebundleitem', 'edit', $service_bundle_item->getID())
            ->withFlash('Service Bundle Item updated');
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
        return $service_bundle_item->with(['service', 'bundle', 'createdBy', 'updatedBy'])->get();
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
            return $response->unauthorized('Unauthorized: Service Bundle Item not deleted');
        }

        $deleted = $service_bundle_item->with(['bundle', 'service', 'createdBy', 'updatedBy'])->get()->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Bundle Item deleted.')->setData('service_bundle_item', $service_bundle_item);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_bundle_items = ServiceBundleItem::new()
                ->with(['bundle', 'service', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_bundle_items)) {
                return $response
                    ->setData('service_bundle_items', [])
                    ->setMessage('No service Bundle Items found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_bundle_items', $service_bundle_items)
                ->setMessage('Service Bundle Items retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Bundle Item indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Bundle Items: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceBundleItem $service_bundle_item
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceBundleItem $service_bundle_item, Response $response)
    {
        try {
            $service_bundle_item = ServiceBundleItem::new()
                ->with(['bundle', 'service', 'createdBy', 'updatedBy'])
                ->find($service_bundle_item->getID());

            if (empty($service_bundle_item)) {
                return $response
                    ->setData('service_bundle_item', null)
                    ->setMessage('Service Bundle Item not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_bundle_item', $service_bundle_item)
                ->setMessage('Service Bundle Item retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Bundle Item showRest error: ' . $e->getMessage());
            return $response
                ->setError('api', 'Failed to retrieve Service Bundle Item')
                ->setMessage('An error occurred while retrieving Service Bundle Item', 'error')
                ->setStatus(500);
        }
    }
}
