<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceComplexity;
use MakerMaker\Http\Fields\ServiceComplexityFields;
use MakerMaker\Models\Service;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;
use TypeRocket\Models\WPUser;

class ServiceComplexityController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_complexities.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceComplexity::class)->useErrors()->useOld();
        return View::new('service_complexities.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceComplexityFields $fields, ServiceComplexity $service_complexity, Response $response, AuthUser $user)
    {
        if (!$service_complexity->can('create')) {
            $response->unauthorized('Unauthorized: ServiceComplexity not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_complexity->save($fields);

        return tr_redirect()->toPage('servicecomplexity', 'index')
            ->withFlash('Service Complexity Created');
    }

    /**
     * The edit page for admin
     *
     * FIXED: Properly load the ServiceComplexity model with all its data
     * before passing it to the form
     *
     * @param string|ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function edit(ServiceComplexity $service_complexity, AuthUser $user)
    {
        $current_id = $service_complexity->getID();
        $services = $service_complexity->services;
        $createdBy = $service_complexity->createdBy;
        $updatedBy = $service_complexity->updatedBy;
        // tr_dd($services);
        $form = tr_form($service_complexity)->useErrors()->useOld();
        return View::new('service_complexities.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function update(ServiceComplexity $service_complexity, ServiceComplexityFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_complexity->can('update')) {
            $response->unauthorized('Unauthorized: ServiceComplexity not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_complexity->save($fields);

        return tr_redirect()->toPage('servicecomplexity', 'edit', $service_complexity->getID())
            ->withFlash('Service Complexity Updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function show(ServiceComplexity $service_complexity)
    {
        return $service_complexity->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function delete(ServiceComplexity $service_complexity)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function destroy(ServiceComplexity $service_complexity, Response $response)
    {
        if (!$service_complexity->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceComplexity not deleted');
        }

        // Check if this complexity is still being used by services
        $servicesCount = $service_complexity->services()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} service(s) still use this complexity. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_complexity->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('ServiceComplexity deleted.')->setData('service_complexity', $service_complexity);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceComplexities = ServiceComplexity::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceComplexities)) {
                return $response
                    ->setData('service_complexities', [])
                    ->setMessage('No service complexities found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_complexities', $serviceComplexities)
                ->setMessage('Service complexities retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceComplexity indexRest error: ' . $e->getMessage());
            return $response
                ->setError('api', 'Failed to retrieve service complexities')
                ->setMessage('An error occurred while retrieving service complexities', 'error')
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceComplexity $service_complexity
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceComplexity $service_complexity, Response $response)
    {
        try {
            $service_complexity = ServiceComplexity::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($service_complexity->getID());

            if (empty($service_complexity)) {
                return $response
                    ->setData('service_complexity', null)
                    ->setMessage('Service complexity not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_complexity', $service_complexity)
                ->setMessage('Service complexity retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceComplexity showRest error: ' . $e->getMessage());
            return $response
                ->setError('api', 'Failed to retrieve service complexity')
                ->setMessage('An error occurred while retrieving service complexity', 'error')
                ->setStatus(500);
        }
    }
}
