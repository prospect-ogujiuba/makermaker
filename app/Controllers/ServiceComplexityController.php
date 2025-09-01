<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceComplexity;
use MakerMaker\Http\Fields\ServiceComplexityFields;
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
        return View::new('service_complexities.form', compact( 'user'));
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

        $service_complexity->created_by = $user->ID;
        $service_complexity->updated_by = $user->ID;
        $service_complexity->save($fields);

        return tr_redirect()->toPage('servicecomplexity', 'index')
            ->withFlash('Service Complexity Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function edit(ServiceComplexity $service_complexity, AuthUser $user)
    {
        return View::new('service_complexities.form', compact('service_complexity', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function update(ServiceComplexity $service_complexity, ServiceComplexityFields $fields, Response $response)
    {

        if (!$service_complexity->can('update')) {
            $response->unauthorized('Unauthorized: ServiceComplexity not updated')->abort();
        }

        $service_complexity->save($fields);

        return tr_redirect()->toPage('servicecomplexity', 'edit', $service_complexity->getID())
            ->withFlash('Info Session Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceComplexity $service_complexity
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
     * @param string|ServiceComplexity $service_complexity
     *
     * @return mixed
     */
    public function delete(ServiceComplexity $service_complexity)
    {
        // TODO: Implement delete() method.
    }

    /** 
     * Destroy item 
     * 
     * AJAX requests and normal requests can be made to this action 
     * 
     * @param string|ServiceComplexity $service_complexity 
     * 
     * @return mixed 
     */
    public function destroy(ServiceComplexity $service_complexity, Response $response)
    {

        if (!$service_complexity->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceComplexity not deleted');
        }

        // Check if this complexity is still being used by services using TypeRocket relationship
        $servicesCount = $service_complexity->services()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} service(s) still use this complexity. Reassign or remove them first.")
                ->setStatus(409);
        }

        // Attempt to delete using TypeRocket's delete method
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
     * @return \TypeRocket\Http\Response|array
     */

    public function indexRest()
    {
        try {
            $serviceComplexities = ServiceComplexity::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();
            if (empty($serviceComplexities)) {
                return \TypeRocket\Http\Response::getFromContainer()
                    ->setData('service_complexities', [])
                    ->setMessage('No service complexities found', 'info')
                    ->setStatus(200);
            }
            return \TypeRocket\Http\Response::getFromContainer()
                ->setData('service_complexities', $serviceComplexities)
                ->setMessage('Service complexities retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceComplexity indexRest error: ' . $e->getMessage());
            return \TypeRocket\Http\Response::getFromContainer()
                ->setError('api', 'Failed to retrieve service complexities')
                ->setMessage('An error occurred while retrieving service complexities', 'error')
                ->setStatus(500);
        }
    }

    /**
     * The index function for API
     *
     * Returns all service complexities with their related services.
     * Includes error handling and response formatting following TypeRocket patterns.
     *
     * @return \TypeRocket\Http\Response|array
     */
    public function showRest(ServiceComplexity $service_complexity)
    {
        try {
            // Get the service complexity records with eager loaded services relationship
            $service_complexity = ServiceComplexity::new()->with(['services', 'createdBy', 'updatedBy'])->find($service_complexity->getID());

            // Check if we have any results
            if (empty($service_complexity)) {
                return \TypeRocket\Http\Response::getFromContainer()
                    ->setData('service_complexity', [])
                    ->setMessage('No service complexity found', 'info')
                    ->setStatus(200);
            }

            // Return successful response with data
            return \TypeRocket\Http\Response::getFromContainer()
                ->setData('service_complexity', $service_complexity)
                ->setMessage('Service complexity retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            // Log the error for debugging
            error_log('ServiceComplexity showRest error: ' . $e->getMessage());

            // Return error response
            return \TypeRocket\Http\Response::getFromContainer()
                ->setError('api', 'Failed to retrieve service complexity')
                ->setMessage('An error occurred while retrieving service complexity', 'error')
                ->setStatus(500);
        }
    }
}
