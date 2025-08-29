<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceAttributeValue;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceAttributeValueController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new ('service_attribute_values.index');
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
     * @param string|ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function edit(ServiceAttributeValue $service_attribute_value)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function update(ServiceAttributeValue $service_attribute_value)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function show(ServiceAttributeValue $service_attribute_value)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function delete(ServiceAttributeValue $service_attribute_value)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAttributeValue $service_attribute_value
     *
     * @return mixed
     */
    public function destroy(ServiceAttributeValue $service_attribute_value)
    {
        // TODO: Implement destroy() method.
    }
}