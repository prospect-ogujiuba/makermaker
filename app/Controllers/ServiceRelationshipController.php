<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceRelationship;
use MakerMaker\Http\Fields\ServiceRelationshipFields;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceRelationshipController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_relationships.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceRelationship::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_relationships.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceRelationshipFields $fields, ServiceRelationship $service_relationship, Response $response, AuthUser $user)
    {
        if (!$service_relationship->can('create')) {
            $response->unauthorized('Unauthorized: Service Delivery_assignment not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_relationship->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'index')
            ->withFlash('Service Delivery_assignment Created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function edit(ServiceRelationship $service_relationship, AuthUser $user)
    {
        $current_id = $service_relationship->getID();
        $createdBy = $service_relationship->createdBy;
        $updatedBy = $service_relationship->updatedBy;

        $form = tr_form($service_relationship)->useErrors()->useOld()->useConfirm();
        return View::new('service_relationships.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function update(ServiceRelationship $service_relationship, ServiceRelationshipFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_relationship->can('update')) {
            $response->unauthorized('Unauthorized: ServiceRelationship not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_relationship->save($fields);

        return tr_redirect()->toPage('servicedeliveryassignment', 'edit', $service_relationship->getID())
            ->withFlash('Service Delivery_assignment Updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function show(ServiceRelationship $service_relationship)
    {
        return $service_relationship->with(['service', 'addonService', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function delete(ServiceRelationship $service_relationship)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceRelationship $service_relationship
     *
     * @return mixed
     */
    public function destroy(ServiceRelationship $service_relationship, Response $response)
    {
        if (!$service_relationship->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceRelationship not deleted');
        }

        $servicesCount = $service_relationship->service()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} Service Relationship(s) still use this. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_relationship->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Relationship deleted.')->setData('service_pricing_model', $service_relationship);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceDelServiceRelationship = ServiceRelationship::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceDelServiceRelationship)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No service Delivery_assignments found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceDelServiceRelationship)
                ->setMessage('Service Delivery_assignment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceRelationship indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Relationship: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
