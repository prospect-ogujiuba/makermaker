<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceDeliverableAssignmentFields;
use MakerMaker\Models\ServiceDeliverableAssignment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceDeliverableAssignmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_deliverable_assignments.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceDeliverableAssignment::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_deliverable_assignments.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceDeliverableAssignmentFields $fields, ServiceDeliverableAssignment $service_deliverable_assingment, Response $response, AuthUser $user)
    {
        if (!$service_deliverable_assingment->can('create')) {
            $response->unauthorized('Unauthorized: Service Coverage not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_deliverable_assingment->save($fields);

        return tr_redirect()->toPage('servicedeliverableassignment', 'index')
            ->withFlash('Service Coverage created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assingment
     *
     * @return mixed
     */
    public function edit(ServiceDeliverableAssignment $service_deliverable_assingment, AuthUser $user)
    {
        $current_id = $service_deliverable_assingment->getID();
        $createdBy = $service_deliverable_assingment->createdBy;
        $updatedBy = $service_deliverable_assingment->updatedBy;

        $form = tr_form($service_deliverable_assingment)->useErrors()->useOld()->useConfirm();
        return View::new('service_deliverable_assignments.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assingment
     *
     * @return mixed
     */
    public function update(ServiceDeliverableAssignment $service_deliverable_assingment, ServiceDeliverableAssignmentFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_deliverable_assingment->can('update')) {
            $response->unauthorized('Unauthorized: Service Deliverable Assignment not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_deliverable_assingment->save($fields);

        return tr_redirect()->toPage('servicedeliverableassignment', 'edit', $service_deliverable_assingment->getID())
            ->withFlash('Service Coverage updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assingment
     *
     * @return mixed
     */
    public function show(ServiceDeliverableAssignment $service_deliverable_assingment)
    {
        return $service_deliverable_assingment->with(['service', 'addonService', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assingment
     *
     * @return mixed
     */
    public function delete(ServiceDeliverableAssignment $service_deliverable_assingment)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assingment
     *
     * @return mixed
     */
    public function destroy(ServiceDeliverableAssignment $service_deliverable_assingment, Response $response)
    {
        if (!$service_deliverable_assingment->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Deliverable Assignment not deleted');
        }

        $service_count = $service_deliverable_assingment->service()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this.")
                ->setStatus(409);
        }

        $deleted = $service_deliverable_assingment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Coverage deleted.')->setData('service_pricing_model', $service_deliverable_assingment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceDeliverableAssignment = ServiceDeliverableAssignment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDeliverableAssignment)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No Service Coverage Assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceDeliverableAssignment)
                ->setMessage('Service Coverage retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceDeliverableAssignment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Coverage: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
