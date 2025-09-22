<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceEquipmentFields;
use MakerMaker\Models\ServiceEquipment;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceEquipmentController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_equipment.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceEquipment::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_equipment.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceEquipmentFields $fields, ServiceEquipment $service_equipment, Response $response, AuthUser $user)
    {
        if (!$service_equipment->can('create')) {
            $response->unauthorized('Unauthorized: Service Equipment not created')->abort();
        }

        autoGenerateCode($fields, 'sku', 'name', '-', $fields['manufacturer'], 'prefix', true);

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_equipment->save($fields);

        return tr_redirect()->toPage('serviceequipment', 'index')
            ->withFlash('Service Equipment created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function edit(ServiceEquipment $service_equipment, AuthUser $user)
    {
        $current_id = $service_equipment->getID();
        $services = $service_equipment->services;
        $createdBy = $service_equipment->createdBy;
        $updatedBy = $service_equipment->updatedBy;

        $form = tr_form($service_equipment)->useErrors()->useOld()->useConfirm();
        return View::new('service_equipment.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function update(ServiceEquipment $service_equipment, ServiceEquipmentFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_equipment->can('update')) {
            $response->unauthorized('Unauthorized: Service Equipment not updated')->abort();
        }

        autoGenerateCode($fields, 'sku', 'name', '-', $fields['manufacturer'], 'prefix', true);

        $fields['updated_by'] = $user->ID;

        $service_equipment->save($fields);

        return tr_redirect()->toPage('serviceequipment', 'edit', $service_equipment->getID())
            ->withFlash('Service Equipment updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function show(ServiceEquipment $service_equipment)
    {
        return $service_equipment->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function delete(ServiceEquipment $service_equipment)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceEquipment $service_equipment
     *
     * @return mixed
     */
    public function destroy(ServiceEquipment $service_equipment, Response $response)
    {
        if (!$service_equipment->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Equipment not deleted');
        }

        $services = $service_equipment->services()->count('service_id');

        if ($services > 0) {
            return $response
                ->error("Cannot delete: {$services} service(s) still use this Service Equipment.")
                ->setStatus(409)
                ->setData('service_equipment', $service_equipment);
        }

        $deleted = $service_equipment->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Equipment deleted.')->setData('service_equipment', $service_equipment);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_equipment = ServiceEquipment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_equipment)) {
                return $response
                    ->setData('service_equipment', [])
                    ->setMessage('No Service Equipment found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_equipment', $service_equipment)
                ->setMessage('Service Equipment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Equipment indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Equipment: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceEquipment $service_equipment
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceEquipment $service_equipment, Response $response)
    {
        try {
            $service_equipment = ServiceEquipment::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($service_equipment->getID());

            if (empty($service_equipment)) {
                return $response
                    ->setData('service_equipment', null)
                    ->setMessage('Service Equipment not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_equipment', $service_equipment)
                ->setMessage('Service Equipment retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Equipment showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Equipment', 'error')
                ->setStatus(500);
        }
    }
}
