<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceAddon;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServiceAddonController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_addons.index');
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
     * @param string|ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function edit(ServiceAddon $service_addon)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function update(ServiceAddon $service_addon)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function show(ServiceAddon $service_addon)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function delete(ServiceAddon $service_addon)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function destroy(ServiceAddon $service_addon)
    {
        // TODO: Implement destroy() method.
    }
}