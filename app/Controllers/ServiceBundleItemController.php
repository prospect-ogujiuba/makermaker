<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceBundleItem;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

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
    public function add()
    {
        // TODO: Implement add() method.
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function edit(ServiceBundleItem $service_bundle_item)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function update(ServiceBundleItem $service_bundle_item)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function show(ServiceBundleItem $service_bundle_item)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function delete(ServiceBundleItem $service_bundle_item)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceBundleItem $service_bundle_item
     *
     * @return mixed
     */
    public function destroy(ServiceBundleItem $service_bundle_item)
    {
        // TODO: Implement destroy() method.
    }
}