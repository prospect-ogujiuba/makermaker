<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceDeliveryMethodAssignmentFields;
use MakerMaker\Models\ServiceDeliveryMethodAssignment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceDeliveryMethodAssignmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_delivery_method_assignments.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceDeliveryMethodAssignment::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_delivery_method_assignments.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceDeliveryMethodAssignmentFields $fields, ServiceDeliveryMethodAssignment $service_delivery_method_assignment, Response $response, AuthUser $user)
    {
        if (!$service_delivery_method_assignment->can('create')) {
            $response->unauthorized('Unauthorized: Service Delivery_assignment not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_delivery_method_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'index')
            ->withFlash('Service Delivery_assignment Created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function edit(ServiceDeliveryMethodAssignment $service_delivery_method_assignment, AuthUser $user)
    {
        $current_id = $service_delivery_method_assignment->getID();
        $createdBy = $service_delivery_method_assignment->createdBy;
        $updatedBy = $service_delivery_method_assignment->updatedBy;

        $form = tr_form($service_delivery_method_assignment)->useErrors()->useOld()->useConfirm();
        return View::new('service_delivery_method_assignments.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function update(ServiceDeliveryMethodAssignment $service_delivery_method_assignment, ServiceDeliveryMethodAssignmentFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_delivery_method_assignment->can('update')) {
            $response->unauthorized('Unauthorized: ServiceDeliveryMethodAssignment not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_delivery_method_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'edit', $service_delivery_method_assignment->getID())
            ->withFlash('Service Delivery_assignment Updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function show(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        return $service_delivery_method_assignment->with(['service', 'addonService', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function delete(ServiceDeliveryMethodAssignment $service_delivery_method_assignment)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     *
     * @return mixed
     */
    public function destroy(ServiceDeliveryMethodAssignment $service_delivery_method_assignment, Response $response)
    {
        if (!$service_delivery_method_assignment->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceDeliveryMethodAssignment not deleted');
        }

        $servicesCount = $service_delivery_method_assignment->service()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} service Delivery_assignment(s) still use this. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_delivery_method_assignment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Delivery_assignment deleted.')->setData('service_pricing_model', $service_delivery_method_assignment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceDeliveryMethodAssignment = ServiceDeliveryMethodAssignment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDeliveryMethodAssignment)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No service Delivery_assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceDeliveryMethodAssignment)
                ->setMessage('Service Delivery_assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceDeliveryMethodAssignment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve service Delivery_assignment: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
