<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceEquipmentAssignment;
use MakerMaker\Http\Fields\ServiceEquipmentAssignmentFields;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceEquipmentAssignmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_equipment_assignments.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceEquipmentAssignment::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_equipment_assignments.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceEquipmentAssignmentFields $fields, ServiceEquipmentAssignment $service_equipment_assignment, Response $response, AuthUser $user)
    {
        if (!$service_equipment_assignment->can('create')) {
            $response->unauthorized('Unauthorized: Service Equipment Assignment not created')->abort();
        }

        $service_equipment_assignment->created_by = $user->ID;
        $service_equipment_assignment->updated_by = $user->ID;

        $service_equipment_assignment->save($fields);

        return tr_redirect()->toPage('serviceequipmentassignment', 'index')
            ->withFlash('Service Equipment Assignment created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function edit(ServiceEquipmentAssignment $service_equipment_assignment, AuthUser $user)
    {
        $current_id = $service_equipment_assignment->getID();
        $equipment = $service_equipment_assignment->service->equipment;
        $createdBy = $service_equipment_assignment->createdBy;
        $updatedBy = $service_equipment_assignment->updatedBy;

        $form = tr_form($service_equipment_assignment)->useErrors()->useOld()->useConfirm();
        return View::new('service_equipment_assignments.form', compact('form', 'current_id', 'equipment', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function update(ServiceEquipmentAssignment $service_equipment_assignment, ServiceEquipmentAssignmentFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_equipment_assignment->can('update')) {
            $response->unauthorized('Unauthorized: Service Equipment Assignment not updated')->abort();
        }

        $service_equipment_assignment->updated_by = $user->ID;

        $service_equipment_assignment->save($fields);

        return tr_redirect()->toPage('serviceequipmentassignment', 'edit', $service_equipment_assignment->getID())
            ->withFlash('Service Equipment Assignment updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function show(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        return $service_equipment_assignment;
    }

    /**
     * The delete page for admin
     *
     * @param ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function delete(ServiceEquipmentAssignment $service_equipment_assignment)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceEquipmentAssignment $service_equipment_assignment
     *
     * @return mixed
     */
    public function destroy(ServiceEquipmentAssignment $service_equipment_assignment, Response $response)
    {
        if (!$service_equipment_assignment->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Equipment Assignment not deleted');
        }

        $deleted = $service_equipment_assignment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Equipment Assignment deleted.')->setData('service_pricing_model', $service_equipment_assignment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_equipment_assignments = ServiceEquipmentAssignment::new()->get();

            if (empty($service_equipment_assignments)) {
                return $response
                    ->setData('service_equipment_assignments', [])
                    ->setMessage('No Service Equipment Assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_equipment_assignments', $service_equipment_assignments)
                ->setMessage('Service Equipment Assignments retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Equipment Assignment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Equipment Assignments: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceEquipmentAssignment $service_equipment_assignment
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceEquipmentAssignment $service_equipment_assignment, Response $response)
    {
        try {
            $service_equipment_assignment = ServiceEquipmentAssignment::new()
                ->find($service_equipment_assignment->getID());

            if (empty($service_equipment_assignment)) {
                return $response
                    ->setData('service_equipment_assignment', null)
                    ->setMessage('Service Equipment Assignment not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_equipment_assignment', $service_equipment_assignment)
                ->setMessage('Service Equipment Assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Equipment Assignment showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Equipment Assignment', 'error')
                ->setStatus(500);
        }
    }
}
