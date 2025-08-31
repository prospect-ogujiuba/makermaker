<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceEquipmentAssignment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceEquipmentAssignmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_equipment_assignments.index');
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
     * @param string|ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function edit(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function update(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function show(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function delete(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function destroy(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        // TODO: Implement destroy() method.
    }
}