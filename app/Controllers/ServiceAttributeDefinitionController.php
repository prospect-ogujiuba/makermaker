<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceAttributeDefinition;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceAttributeDefinitionController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_attribute_definitions.index');
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
     * @param string|ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function edit(ServiceAttributeDefinition $service_attribute_definition)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function update(ServiceAttributeDefinition $service_attribute_definition)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function show(ServiceAttributeDefinition $service_attribute_definition)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function delete(ServiceAttributeDefinition $service_attribute_definition)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAttributeDefinition $service_attribute_definition
     *
     * @return mixed
     */
    public function destroy(ServiceAttributeDefinition $service_attribute_definition)
    {
        // TODO: Implement destroy() method.
    }
}