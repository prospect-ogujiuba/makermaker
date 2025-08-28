<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServicePrice;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServicePriceController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_prices.index');
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
     * @param string|ServicePrice $service_price
     *
     * @return mixed
     */
    public function edit(ServicePrice $service_price)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServicePrice $service_price
     *
     * @return mixed
     */
    public function update(ServicePrice $service_price)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServicePrice $service_price
     *
     * @return mixed
     */
    public function show(ServicePrice $service_price)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServicePrice $service_price
     *
     * @return mixed
     */
    public function delete(ServicePrice $service_price)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServicePrice $service_price
     *
     * @return mixed
     */
    public function destroy(ServicePrice $service_price)
    {
        // TODO: Implement destroy() method.
    }
}
