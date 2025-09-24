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
            $response->unauthorized('Unauthorized: Service Delivery Method Assignment not created')->abort();
        }

        $service_delivery_method_assignment->created_by = $user->ID;
        $service_delivery_method_assignment->updated_by = $user->ID;

        $service_delivery_method_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliverymethodassignment', 'index')
            ->withFlash('Service Delivery Method Assignment created');
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
            $response->unauthorized('Unauthorized: Service Delivery Method Assignment not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_delivery_method_assignment->save($fields);

        return tr_redirect()->toPage('servicedeliverymethodassignment', 'edit', $service_delivery_method_assignment->getID())
            ->withFlash('Service Delivery Method Assignment updated');
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
        return $service_delivery_method_assignment->with(['service', 'deliveryMethod', 'createdBy', 'updatedBy'])->get();
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
            return $response->unauthorized('Unauthorized: Service Delivery Method Assignment not deleted');
        }

        $deleted = $service_delivery_method_assignment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Delivery Method Assignment deleted.')->setData('service_pricing_model', $service_delivery_method_assignment);
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
                ->with(['service', 'deliveryMethod', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDeliveryMethodAssignment)) {
                return $response
                    ->setData('service_delivery_method_assignment', [])
                    ->setMessage('No Service Delivery Method Assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_delivery_method_assignment', $serviceDeliveryMethodAssignment)
                ->setMessage('Service Delivery Method Assignments retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivery Method Assignment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Delivery Method Assignments: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceDeliveryMethodAssignment $service_delivery_method_assignment
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceDeliveryMethodAssignment $service_delivery_method_assignment, Response $response)
    {
        try {
            $service_delivery_method_assignment = ServiceDeliveryMethodAssignment::new()
                ->with(['service', 'deliveryMethod', 'createdBy', 'updatedBy'])
                ->find($service_delivery_method_assignment->getID());

            if (empty($service_delivery_method_assignment)) {
                return $response
                    ->setData('service_delivery_method_assignment', null)
                    ->setMessage('Service Delivery Method Assignment not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_delivery_method_assignment', $service_delivery_method_assignment)
                ->setMessage('Service Delivery Method Assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Delivery Method Assignment showRest error: ' . $e->getMessage());
            return $response
                ->setError('api', 'Failed to retrieve Service Delivery Method Assignment')
                ->setMessage('An error occurred while retrieving Service Delivery Method Assignment', 'error')
                ->setStatus(500);
        }
    }
}
