<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\DeliverableFields;
use MakerMaker\Models\Deliverable;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class DeliverableController extends Controller
{


    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('deliverables.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(Deliverable::class)->useErrors()->useOld()->useConfirm();
        return View::new('deliverables.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(DeliverableFields $fields, Deliverable $deliverable, Response $response, AuthUser $user)
    {
        if (!$deliverable->can('create')) {
            $response->unauthorized('Unauthorized: Service Deliverable not created')->abort();
        }

        $deliverable->created_by = $user->ID;
        $deliverable->updated_by = $user->ID;

        $deliverable->save($fields);

        $created = $deliverable;

        if ($created === false) {
            return $response
                ->error('Creation failed due to a database error.')
                ->setStatus(500);
        }

        // return $response->success('Service Complexity created.')->setData('service$deliverable', $deliverable);

        return tr_redirect()->toPage('deliverable', 'index')
            ->withFlash('Service Deliverable created');
    }

    /**
     * The edit page for admin
     *
     * @param Deliverable $deliverable
     *
     * @return mixed
     */
    public function edit(Deliverable $deliverable, AuthUser $user)
    {
        $current_id = $deliverable->getID();
        $services = $deliverable->services;
        $createdBy = $deliverable->createdBy;
        $updatedBy = $deliverable->updatedBy;

        $form = tr_form($deliverable)->useErrors()->useOld()->useConfirm();
        return View::new('deliverables.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param Deliverable $deliverable
     *
     * @return mixed
     */
    public function update(Deliverable $deliverable, DeliverableFields $fields, Response $response, AuthUser $user)
    {
        if (!$deliverable->can('update')) {
            $response->unauthorized('Unauthorized: Service Deliverable not updated')->abort();
        }

        $deliverable->updated_by = $user->ID;

        $deliverable->save($fields);

        return tr_redirect()->toPage('deliverable', 'edit', $deliverable->getID())
            ->withFlash('Service Deliverable updated');
    }

    /**
     * The show page for admin
     *
     * @param Deliverable $deliverable
     *
     * @return mixed
     */
    public function show(Deliverable $deliverable)
    {
        return $deliverable;
    }

    /**
     * The delete page for admin
     *
     * @param Deliverable $deliverable
     *
     * @return mixed
     */
    public function delete(Deliverable $deliverable)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param Deliverable $deliverable
     *
     * @return mixed
     */
    public function destroy(Deliverable $deliverable, Response $response)
    {
        if (!$deliverable->can('destroy')) {
            return $response->unauthorized('Unauthorized: Deliverable not deleted');
        }

        $count = $deliverable->services()->count('id');

        if ($count > 0) {
            return $response
                ->error("Cannot delete: {$count} service(s) still use this Service Deliverable.")
                ->setStatus(409)
                ->setData('deliverable', $deliverable);
        }

        $deleted = $deliverable->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Deliverable deleted.')->setData('deliverable', $deliverable);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $deliverables = Deliverable::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($deliverables)) {
                return $response
                    ->setData('deliverables', [])
                    ->setMessage('No Service Deliverables found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('deliverables', $deliverables)
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
     * @param Deliverable $deliverable
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(Deliverable $deliverable, Response $response)
    {
        try {
            $deliverable = Deliverable::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($deliverable->getID());

            if (empty($deliverable)) {
                return $response
                    ->setData('deliverable', null)
                    ->setMessage('Service Deliverable not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('deliverable', $deliverable)
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
