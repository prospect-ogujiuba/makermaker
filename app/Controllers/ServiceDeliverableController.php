<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceDeliverable;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceDeliverableController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_deliverables.index');
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
     * @param string|ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function edit(ServiceDeliverable $service_deliverable)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function update(ServiceDeliverable $service_deliverable)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function show(ServiceDeliverable $service_deliverable)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function delete(ServiceDeliverable $service_deliverable)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function destroy(ServiceDeliverable $service_deliverable)
    {
        // TODO: Implement destroy() method.
    }
}
