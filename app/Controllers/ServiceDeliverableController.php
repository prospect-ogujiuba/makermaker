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
            $response->unauthorized('Unauthorized: ServiceDeliverable not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_deliverable->save($fields);

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
            $response->unauthorized('Unauthorized: ServiceDeliverable not updated')->abort();
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

        $servicesCount = $service_deliverable->services()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} service deliverable(s) still use this. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_deliverable->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('ServiceDeliverable deleted.')->setData('service_pricing_model', $service_deliverable);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceDeliverableServiceDeliverables = ServiceDeliverable::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDeliverableServiceDeliverables)) {
                return $response
                    ->setData('service_deliverables', [])
                    ->setMessage('No service deliverables found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_deliverables', $serviceDeliverableServiceDeliverables)
                ->setMessage('Service deliverable retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceDeliverable indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve service deliverable: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
