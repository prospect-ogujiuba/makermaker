<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceType;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

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
     * @param string|ServiceType $service_type
     *
     * @return mixed
     */
    public function edit(ServiceType $service_type)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceType $service_type
     *
     * @return mixed
     */
    public function update(ServiceType $service_type)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceType $service_type
     *
     * @return mixed
     */
    public function show(ServiceType $service_type)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceType $service_type
     *
     * @return mixed
     */
    public function delete(ServiceType $service_type)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceType $service_type
     *
     * @return mixed
     */
    public function destroy(ServiceType $service_type)
    {
        // TODO: Implement destroy() method.
    }
}