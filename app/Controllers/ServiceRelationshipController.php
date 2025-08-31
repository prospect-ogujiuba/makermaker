<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceRelationship;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceRelationshipController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_relationships.index');
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
     * @param string|ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function edit(ServiceRelationship $service_relationship)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function update(ServiceRelationship $service_relationship)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function show(ServiceRelationship $service_relationship)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function delete(ServiceRelationship $service_relationship)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function destroy(ServiceRelationship $service_relationship)
    {
        // TODO: Implement destroy() method.
    }
}