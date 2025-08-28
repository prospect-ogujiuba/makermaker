<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\Service;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('services.index');
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
     * @param string|Service $service
     *
     * @return mixed
     */
    public function edit(Service $service)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function update(Service $service)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function show(Service $service)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function delete(Service $service)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function destroy(Service $service)
    {
        // TODO: Implement destroy() method.
    }
}