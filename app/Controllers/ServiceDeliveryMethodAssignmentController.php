<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceDeliveryMethodAssignment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceDeliveryMethodAssignmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_delivery_method_assignments.index');
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
     * @param string|ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function edit(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function update(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function show(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function delete(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function destroy(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        // TODO: Implement destroy() method.
    }
}