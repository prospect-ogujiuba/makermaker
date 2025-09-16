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
            $response->unauthorized('Unauthorized: Service Delivery_assignment not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_equipment_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'index')
            ->withFlash('Service Delivery_assignment created');
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
        $createdBy = $service_equipment_assignment->createdBy;
        $updatedBy = $service_equipment_assignment->updatedBy;

        $form = tr_form($service_equipment_assignment)->useErrors()->useOld()->useConfirm();
        return View::new('service_equipment_assignments.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
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
            $response->unauthorized('Unauthorized: ServiceEquipmentAssignment not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_equipment_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'edit', $service_equipment_assignment->getID())
            ->withFlash('Service Delivery_assignment updated');
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
        return $service_equipment_assignment->with(['service', 'addonService', 'createdBy', 'updatedBy'])->get();
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
            return $response->unauthorized('Unauthorized: ServiceEquipmentAssignment not deleted');
        }

        $servicesCount = $service_equipment_assignment->service()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} Service Delivery Assignment(s) still use this. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_equipment_assignment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Delivery Assignment deleted.')->setData('service_pricing_model', $service_equipment_assignment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceDelServiceEquipmentAssignment = ServiceEquipmentAssignment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDelServiceEquipmentAssignment)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No service Delivery_assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceDelServiceEquipmentAssignment)
                ->setMessage('Service Delivery_assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceEquipmentAssignment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Delivery Assignment: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
