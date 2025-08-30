<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceCoverage;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

class ServiceCoverageController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_coverage.index');
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
     * @param string|ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function edit(ServiceCoverage $service_coverage)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function update(ServiceCoverage $service_coverage)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function show(ServiceCoverage $service_coverage)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function delete(ServiceCoverage $service_coverage)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function destroy(ServiceCoverage $service_coverage)
    {
        // TODO: Implement destroy() method.
    }
}