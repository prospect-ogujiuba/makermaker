<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceDeliverableAssignment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceDeliverableAssignmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_deliverable_assignments.index');
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
     * @param string|ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function edit(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function update(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function show(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function delete(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function destroy(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        // TODO: Implement destroy() method.
    }
}