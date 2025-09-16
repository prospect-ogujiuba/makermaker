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
            $response->unauthorized('Unauthorized: Service Relationship not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_relationship->save($fields);

        return tr_redirect()->toPage('servicerelationship', 'index')
            ->withFlash('Service Relationship created');
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
            $response->unauthorized('Unauthorized: Service Relationship not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_relationship->save($fields);

        return tr_redirect()->toPage('servicerelationship', 'edit', $service_relationship->getID())
            ->withFlash('Service Relationship updated');
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
            return $response->unauthorized('Unauthorized: Service Relationship not deleted');
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
            $serviceRelationship = ServiceRelationship::new()
                ->with(['service', 'relatedService', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceRelationship)) {
                return $response
                    ->setData('service_relationship', [])
                    ->setMessage('No Service Relationships found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_relationship', $serviceRelationship)
                ->setMessage('Service Relationships retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Relationship indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Relationships: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceRelationship $service_relationship
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceRelationship $service_relationship, Response $response)
    {
        try {
            $service_relationship = $service_relationship->with(['service', 'relatedService', 'createdBy', 'updatedBy'])->first();

            if (!$service_relationship) {
                return $response
                    ->setMessage('Service Relationship not found', 'error')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_relationship', $service_relationship)
                ->setMessage('Service Relationship retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Relationship showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving the Service Relationship', 'error')
                ->setStatus(500);
        }
    }
}
