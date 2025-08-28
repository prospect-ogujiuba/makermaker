<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceBundle;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceBundleController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_bundles.index');
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
     * @param string|ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function edit(ServiceBundle $service_bundle)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function update(ServiceBundle $service_bundle)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function show(ServiceBundle $service_bundle)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function delete(ServiceBundle $service_bundle)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function destroy(ServiceBundle $service_bundle)
    {
        // TODO: Implement destroy() method.
    }
}