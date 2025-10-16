<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceCoverageFields;
use MakerMaker\Models\ServiceCoverage;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

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
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceCoverage::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_coverage.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceCoverageFields $fields, ServiceCoverage $service_coverage, Response $response, AuthUser $user)
    {
        if (!$service_coverage->can('create')) {
            $response->unauthorized('Unauthorized: Service Coverage not created')->abort();
        }

        $service_coverage->created_by = $user->ID;
        $service_coverage->updated_by = $user->ID;

        $service_coverage->save($fields);

        return tr_redirect()->toPage('servicecoverage', 'index')
            ->withFlash('Service Coverage created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function edit(ServiceCoverage $service_coverage, AuthUser $user)
    {
        $current_id = $service_coverage->getID();
        $createdBy = $service_coverage->createdBy;
        $updatedBy = $service_coverage->updatedBy;

        $form = tr_form($service_coverage)->useErrors()->useOld()->useConfirm();
        return View::new('service_coverage.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function update(ServiceCoverage $service_coverage, ServiceCoverageFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_coverage->can('update')) {
            $response->unauthorized('Unauthorized: Service Coverage not updated')->abort();
        }

        $service_coverage->updated_by = $user->ID;

        $service_coverage->save($fields);

        return tr_redirect()->toPage('servicecoverage', 'edit', $service_coverage->getID())
            ->withFlash('Service Coverage updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function show(ServiceCoverage $service_coverage)
    {
        return $service_coverage;
    }

    /**
     * The delete page for admin
     *
     * @param ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function delete(ServiceCoverage $service_coverage)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceCoverage $service_coverage
     *
     * @return mixed
     */
    public function destroy(ServiceCoverage $service_coverage, Response $response)
    {
        if (!$service_coverage->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Coverage not deleted');
        }

        $service_count = $service_coverage->service()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} Service(s) still use this.")
                ->setStatus(409)
                ->setData('service_coverage', $service_coverage);
        }

        $deleted = $service_coverage->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Coverage deleted.')->setData('service_coverage', $service_coverage);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_coverage = ServiceCoverage::new()->get();
            if (empty($service_coverage)) {
                return $response
                    ->setData('service_coverage', [])
                    ->setMessage('No Service Coverages found', 'info')
                    ->setStatus(200);
            }
            return $response
                ->setData('service_coverage', $service_coverage)
                ->setMessage('Service Coverages retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Coverage indexRest error: ' . $e->getMessage());
            return $response
                ->error('Failed to retrieve Service Coverages: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceCoverage $service_coverage
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceCoverage $service_coverage, Response $response)
    {
        try {
            $service_coverage = ServiceCoverage::new()
                ->find($service_coverage->getID());
            if (empty($service_coverage)) {
                return $response
                    ->setData('service_coverage', null)
                    ->setMessage('Service Coverage not found', 'info')
                    ->setStatus(404);
            }
            return $response
                ->setData('service_coverage', $service_coverage)
                ->setMessage('Service Coverage retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Coverage showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Coverage', 'error')
                ->setStatus(500);
        }
    }
    
}
