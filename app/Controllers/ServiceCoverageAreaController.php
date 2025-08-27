<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceCoverageArea;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceCoverageAreaController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_coverage_areas.index');
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
     * @param string|ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function edit(ServiceCoverageArea $service_coverage_area)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function update(ServiceCoverageArea $service_coverage_area)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function show(ServiceCoverageArea $service_coverage_area)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function delete(ServiceCoverageArea $service_coverage_area)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function destroy(ServiceCoverageArea $service_coverage_area)
    {
        // TODO: Implement destroy() method.
    }
}
