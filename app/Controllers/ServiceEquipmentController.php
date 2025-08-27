<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceEquipment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceEquipmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_equipment.index');
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
     * @param string|ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function edit(ServiceEquipment $service_equipment)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function update(ServiceEquipment $service_equipment)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function show(ServiceEquipment $service_equipment)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function delete(ServiceEquipment $service_equipment)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function destroy(ServiceEquipment $service_equipment)
    {
        // TODO: Implement destroy() method.
    }
}