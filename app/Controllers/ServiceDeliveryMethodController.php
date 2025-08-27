<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceDeliveryMethod;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

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
     * @param string|ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function edit(ServiceDeliveryMethod $service_delivery_method)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function update(ServiceDeliveryMethod $service_delivery_method)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function show(ServiceDeliveryMethod $service_delivery_method)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function delete(ServiceDeliveryMethod $service_delivery_method)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliveryMethod $service_delivery_method
     *
     * @return mixed
     */
    public function destroy(ServiceDeliveryMethod $service_delivery_method)
    {
        // TODO: Implement destroy() method.
    }
}
