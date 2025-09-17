<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceCoverageAreaFields;
use MakerMaker\Models\ServiceCoverageArea;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

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
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceCoverageArea::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_coverage_areas.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceCoverageAreaFields $fields, ServiceCoverageArea $service_coverage_area, Response $response, AuthUser $user)
    {
        if (!$service_coverage_area->can('create')) {
            $response->unauthorized('Unauthorized: ServiceCoverageArea not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_coverage_area->save($fields);

        return tr_redirect()->toPage('servicecoveragearea', 'index')
            ->withFlash('Service Coverage Area created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function edit(ServiceCoverageArea $service_coverage_area, AuthUser $user)
    {
        $current_id = $service_coverage_area->getID();
        $serviceCoverages = $service_coverage_area->serviceCoverages;
        $createdBy = $service_coverage_area->createdBy;
        $updatedBy = $service_coverage_area->updatedBy;

        $form = tr_form($service_coverage_area)->useErrors()->useOld()->useConfirm();
        return View::new('service_coverage_areas.form', compact('form', 'current_id', 'serviceCoverages', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function update(ServiceCoverageArea $service_coverage_area, ServiceCoverageAreaFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_coverage_area->can('update')) {
            $response->unauthorized('Unauthorized: ServiceCoverageArea not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_coverage_area->save($fields);

        return tr_redirect()->toPage('servicecoveragearea', 'edit', $service_coverage_area->getID())
            ->withFlash('Service Coverage Area updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function show(ServiceCoverageArea $service_coverage_area)
    {
        return $service_coverage_area->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function delete(ServiceCoverageArea $service_coverage_area)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceCoverageArea $service_coverage_area
     *
     * @return mixed
     */
    public function destroy(ServiceCoverageArea $service_coverage_area, Response $response)
    {
        if (!$service_coverage_area->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceCoverageArea not deleted');
        }

        $serviceCoveragesCount = $service_coverage_area->serviceCoverages()->count();

        if ($serviceCoveragesCount > 0) {
            return $response
                ->error("Cannot delete: {$serviceCoveragesCount} service coverage(s) still use this coverage Area.")
                ->setStatus(409);
        }

        $deleted = $service_coverage_area->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('ServiceCoverageArea deleted.')->setData('service_pricing_model', $service_coverage_area);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceCoverages = ServiceCoverageArea::new()
                ->with(['serviceCoverages', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceCoverages)) {
                return $response
                    ->setData('service_coverage_areas', [])
                    ->setMessage('No service coverage Areas found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_coverage_areas', $serviceCoverages)
                ->setMessage('Service coverage areas retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceCoverageArea indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve service coverage areas: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceCoverageArea $service_coverage_area
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceCoverageArea $service_coverage_area, Response $response)
    {
        try {
            $service_coverage_area = ServiceCoverageArea::new()
                ->with(['serviceCoverages', 'createdBy', 'updatedBy'])
                ->find($service_coverage_area->getID());

            if (empty($service_coverage_area)) {
                return $response
                    ->setData('service_complexity', null)
                    ->setMessage('Service Complexity not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_complexity', $service_coverage_area)
                ->setMessage('Service Complexity retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceCoverageArea showRest error: ' . $e->getMessage());
            return $response
                ->setError('api', 'Failed to retrieve service complexity')
                ->setMessage('An error occurred while retrieving service complexity', 'error')
                ->setStatus(500);
        }
    }
}
