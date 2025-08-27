<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceCategory;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceCategoryController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_categories.index');
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
     * @param string|ServiceCategory $service_category
     *
     * @return mixed
     */
    public function edit(ServiceCategory $service_category)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCategory $service_category
     *
     * @return mixed
     */
    public function update(ServiceCategory $service_category)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceCategory $service_category
     *
     * @return mixed
     */
    public function show(ServiceCategory $service_category)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceCategory $service_category
     *
     * @return mixed
     */
    public function delete(ServiceCategory $service_category)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCategory $service_category
     *
     * @return mixed
     */
    public function destroy(ServiceCategory $service_category)
    {
        // TODO: Implement destroy() method.
    }
}