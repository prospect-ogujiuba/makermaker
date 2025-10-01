<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\BundleItem;
use MakerMaker\Http\Fields\BundleItemFields;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class BundleItemController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('bundle_items.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(BundleItem::class)->useErrors()->useOld()->useConfirm();
        return View::new('bundle_items.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(BundleItemFields $fields, BundleItem $bundle_item, Response $response, AuthUser $user)
    {
        if (!$bundle_item->can('create')) {
            $response->unauthorized('Unauthorized: Bundle Item not created')->abort();
        }

        $bundle_item->created_by = $user->ID;
        $bundle_item->updated_by = $user->ID;

        $bundle_item->save($fields);

        return tr_redirect()->toPage('bundleitem', 'index')
            ->withFlash('Bundle Item created');
    }

    /**
     * The edit page for admin
     *
     * @param BundleItem $bundle_item
     *
     * @return mixed
     */
    public function edit(BundleItem $bundle_item, AuthUser $user)
    {
        $current_id = $bundle_item->getID();
        $services = $bundle_item->bundle->services;
        $createdBy = $bundle_item->createdBy;
        $updatedBy = $bundle_item->updatedBy;

        $form = tr_form($bundle_item)->useErrors()->useOld()->useConfirm();
        return View::new('bundle_items.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param BundleItem $bundle_item
     *
     * @return mixed
     */
    public function update(BundleItem $bundle_item, BundleItemFields $fields, Response $response, AuthUser $user)
    {
        if (!$bundle_item->can('update')) {
            $response->unauthorized('Unauthorized: Bundle Item not updated')->abort();
        }

        $bundle_item->updated_by = $user->ID;

        $bundle_item->save($fields);

        return tr_redirect()->toPage('bundleitem', 'edit', $bundle_item->getID())
            ->withFlash('Bundle Item updated');
    }

    /**
     * The show page for admin
     *
     * @param BundleItem $bundle_item
     *
     * @return mixed
     */
    public function show(BundleItem $bundle_item)
    {
        return $bundle_item;
    }

    /**
     * The delete page for admin
     *
     * @param BundleItem $bundle_item
     *
     * @return mixed
     */
    public function delete(BundleItem $bundle_item)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param BundleItem $bundle_item
     *
     * @return mixed
     */
    public function destroy(BundleItem $bundle_item, Response $response)
    {
        if (!$bundle_item->can('destroy')) {
            return $response->unauthorized('Unauthorized: Bundle Item not deleted');
        }

        $deleted = $bundle_item->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Bundle Item deleted.')->setData('bundle_item', $bundle_item);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $bundle_items = BundleItem::new()->get();

            if (empty($bundle_items)) {
                return $response
                    ->setData('bundle_items', [])
                    ->setMessage('No Bundle Items found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('bundle_items', $bundle_items)
                ->setMessage('Bundle Items retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Bundle Item indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Bundle Items: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param BundleItem $bundle_item
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(BundleItem $bundle_item, Response $response)
    {
        try {
            $bundle_item = BundleItem::new()
                ->find($bundle_item->getID());

            if (empty($bundle_item)) {
                return $response
                    ->setData('bundle_item', null)
                    ->setMessage('Bundle Item not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('bundle_item', $bundle_item)
                ->setMessage('Bundle Item retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Bundle Item showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Bundle Item', 'error')
                ->setStatus(500);
        }
    }
}
