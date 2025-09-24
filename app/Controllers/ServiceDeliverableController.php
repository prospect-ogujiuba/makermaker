<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceDeliverableFields;
use MakerMaker\Models\ServiceDeliverable;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceDeliverableController extends Controller
{


    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_deliverables.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceDeliverable::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_deliverables.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceDeliverableFields $fields, ServiceDeliverable $service_deliverable, Response $response, AuthUser $user)
    {
        if (!$service_deliverable->can('create')) {
            $response->unauthorized('Unauthorized: Service Deliverable not created')->abort();
        }

        $service_deliverable->created_by = $user->ID;
        $service_deliverable->updated_by = $user->ID;

        $service_deliverable->save($fields);

        $created = $service_deliverable;

        if ($created === false) {
            return $response
                ->error('Creation failed due to a database error.')
                ->setStatus(500);
        }

        // return $response->success('Service Complexity created.')->setData('service$service_deliverable', $service_deliverable);

        return tr_redirect()->toPage('servicedeliverable', 'index')
            ->withFlash('Service Deliverable created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function edit(ServiceDeliverable $service_deliverable, AuthUser $user)
    {
        $current_id = $service_deliverable->getID();
        $services = $service_deliverable->services;
        $createdBy = $service_deliverable->createdBy;
        $updatedBy = $service_deliverable->updatedBy;

        $form = tr_form($service_deliverable)->useErrors()->useOld()->useConfirm();
        return View::new('service_deliverables.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function update(ServiceDeliverable $service_deliverable, ServiceDeliverableFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_deliverable->can('update')) {
            $response->unauthorized('Unauthorized: Service Deliverable not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_deliverable->save($fields);

        return tr_redirect()->toPage('servicedeliverable', 'edit', $service_deliverable->getID())
            ->withFlash('Service Deliverable updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function show(ServiceDeliverable $service_deliverable)
    {
        return $service_deliverable->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function delete(ServiceDeliverable $service_deliverable)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliverable $service_deliverable
     *
     * @return mixed
     */
    public function destroy(ServiceDeliverable $service_deliverable, Response $response)
    {
        if (!$service_deliverable->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceDeliverable not deleted');
        }

        $service_count = $service_deliverable->services()->count('service_id');

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this Service Deliverable.")
                ->setStatus(409)
                ->setData('service_deliverable', $service_deliverable);
        }

        $deleted = $service_deliverable->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Deliverable deleted.')->setData('service_deliverable', $service_deliverable);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_deliverables = ServiceDeliverable::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_deliverables)) {
                return $response
                    ->setData('service_deliverables', [])
                    ->setMessage('No Service Deliverables found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_deliverables', $service_deliverables)
                ->setMessage('Service Deliverables retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Deliverable indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Deliverables: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceDeliverable $service_deliverable
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceDeliverable $service_deliverable, Response $response)
    {
        try {
            $service_deliverable = ServiceDeliverable::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($service_deliverable->getID());

            if (empty($service_deliverable)) {
                return $response
                    ->setData('service_deliverable', null)
                    ->setMessage('Service Deliverable not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_deliverable', $service_deliverable)
                ->setMessage('Service Deliverable retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Deliverable showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Deliverable', 'error')
                ->setStatus(500);
        }
    }
}
