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
    public function create(ServiceDeliverableAssignmentFields $fields, ServiceDeliverableAssignment $service_deliverable_assignment, Response $response, AuthUser $user)
    {
        if (!$service_deliverable_assignment->can('create')) {
            $response->unauthorized('Unauthorized: Service Deliverable Assignment not created')->abort();
        }

        $service_deliverable_assignment->created_by = $user->ID;
        $service_deliverable_assignment->updated_by = $user->ID;

        $service_deliverable_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliverableassignment', 'index')
            ->withFlash('Service Deliverable Assignment created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function edit(ServiceDeliverableAssignment $service_deliverable_assignment, AuthUser $user)
    {
        $current_id = $service_deliverable_assignment->getID();
        $createdBy = $service_deliverable_assignment->createdBy;
        $updatedBy = $service_deliverable_assignment->updatedBy;

        $form = tr_form($service_deliverable_assignment)->useErrors()->useOld()->useConfirm();
        return View::new('service_deliverable_assignments.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function update(ServiceDeliverableAssignment $service_deliverable_assignment, ServiceDeliverableAssignmentFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_deliverable_assignment->can('update')) {
            $response->unauthorized('Unauthorized: Service Deliverable Assignment not updated')->abort();
        }

        $service_deliverable_assignment->updated_by = $user->ID;

        $service_deliverable_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliverableassignment', 'edit', $service_deliverable_assignment->getID())
            ->withFlash('Service Deliverable Assignment updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function show(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        return $service_deliverable_assignment;
    }

    /**
     * The delete page for admin
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function delete(ServiceDeliverableAssignment $service_deliverable_assignment)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assignment
     *
     * @return mixed
     */
    public function destroy(ServiceDeliverableAssignment $service_deliverable_assignment, Response $response)
    {
        if (!$service_deliverable_assignment->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Deliverable Assignment not deleted');
        }

        $service_count = $service_deliverable_assignment->service()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this.")
                ->setStatus(409)
                ->setData('service_deliverable_assignment', $service_deliverable_assignment);
        }

        $deleted = $service_deliverable_assignment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Deliverable Assignment deleted.')->setData('service_deliverable_assignment', $service_deliverable_assignment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_deliverable_assignments = ServiceDeliverableAssignment::new()
                ->get();

            if (empty($service_deliverable_assignments)) {
                return $response
                    ->setData('service_deliverable_assignments', [])
                    ->setMessage('No Service Deliverable Assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_deliverable_assignments', $service_deliverable_assignments)
                ->setMessage('Service Deliverable Assignments retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Deliverable Assignment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Deliverable Assignments: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceDeliverableAssignment $service_deliverable_assignment
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceDeliverableAssignment $service_deliverable_assignment, Response $response)
    {
        try {
            $service_deliverable_assignment = ServiceDeliverableAssignment::new()
                ->find($service_deliverable_assignment->getID());

            if (empty($service_deliverable_assignment)) {
                return $response
                    ->setData('service_deliverable_assignment', null)
                    ->setMessage('Service Delivaerable Assignment not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_deliverable_assignment', $service_deliverable_assignment)
                ->setMessage('Service Delivaerable Assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivaerable Assignment showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Delivaerable Assignment', 'error')
                ->setStatus(500);
        }
    }
}
