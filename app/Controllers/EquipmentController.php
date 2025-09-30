<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\EquipmentFields;
use MakerMaker\Models\Equipment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class EquipmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('equipment.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(Equipment::class)->useErrors()->useOld()->useConfirm();
        return View::new('equipment.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(EquipmentFields $fields, Equipment $equipment, Response $response, AuthUser $user)
    {
        if (!$equipment->can('create')) {
            $response->unauthorized('Unauthorized: Equipment not created')->abort();
        }

        autoGenerateCode($fields, 'sku', 'name', '-', $fields['manufacturer'], 'prefix', true);

        $equipment->created_by = $user->ID;
        $equipment->updated_by = $user->ID;

        $equipment->save($fields);

        return tr_redirect()->toPage('equipment', 'index')
            ->withFlash('Equipment created');
    }

    /**
     * The edit page for admin
     *
     * @param Equipment $equipment
     *
     * @return mixed
     */
    public function edit(Equipment $equipment, AuthUser $user)
    {
        $current_id = $equipment->getID();
        $services = $equipment->services;
        $createdBy = $equipment->createdBy;
        $updatedBy = $equipment->updatedBy;

        $form = tr_form($equipment)->useErrors()->useOld()->useConfirm();
        return View::new('equipment.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param Equipment $equipment
     *
     * @return mixed
     */
    public function update(Equipment $equipment, EquipmentFields $fields, Response $response, AuthUser $user)
    {
        if (!$equipment->can('update')) {
            $response->unauthorized('Unauthorized: Equipment not updated')->abort();
        }

        autoGenerateCode($fields, 'sku', 'name', '-', $fields['manufacturer'], 'prefix', true);

        $equipment->updated_by = $user->ID;

        $equipment->save($fields);

        return tr_redirect()->toPage('equipment', 'edit', $equipment->getID())
            ->withFlash('Equipment updated');
    }

    /**
     * The show page for admin
     *
     * @param Equipment $equipment
     *
     * @return mixed
     */
    public function show(Equipment $equipment)
    {
        return $equipment->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param Equipment $equipment
     *
     * @return mixed
     */
    public function delete(Equipment $equipment)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param Equipment $equipment
     *
     * @return mixed
     */
    public function destroy(Equipment $equipment, Response $response)
    {
        if (!$equipment->can('destroy')) {
            return $response->unauthorized('Unauthorized: Equipment not deleted');
        }

        $services = $equipment->services()->count('service_id');

        if ($services > 0) {
            return $response
                ->error("Cannot delete: {$services} service(s) still use this Equipment.")
                ->setStatus(409)
                ->setData('equipment', $equipment);
        }

        $deleted = $equipment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Equipment deleted.')->setData('equipment', $equipment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $equipment = Equipment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($equipment)) {
                return $response
                    ->setData('equipment', [])
                    ->setMessage('No Equipment found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('equipment', $equipment)
                ->setMessage('Equipment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Equipment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Equipment: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param Equipment $equipment
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(Equipment $equipment, Response $response)
    {
        try {
            $equipment = Equipment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($equipment->getID());

            if (empty($equipment)) {
                return $response
                    ->setData('equipment', null)
                    ->setMessage('Equipment not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('equipment', $equipment)
                ->setMessage('Equipment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Equipment showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Equipment', 'error')
                ->setStatus(500);
        }
    }
}
