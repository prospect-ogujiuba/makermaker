<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceAddonFields;
use MakerMaker\Models\ServiceAddon;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceAddonController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_addons.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceAddon::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_addons.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceAddonFields $fields, ServiceAddon $service_addon, Response $response, AuthUser $user)
    {
        if (!$service_addon->can('create')) {
            $response->unauthorized('Unauthorized: Service Addon not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_addon->save($fields);

        return tr_redirect()->toPage('serviceaddon', 'index')
            ->withFlash('Service Addon created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function edit(ServiceAddon $service_addon, AuthUser $user)
    {
        $current_id = $service_addon->getID();
        $createdBy = $service_addon->createdBy;
        $updatedBy = $service_addon->updatedBy;

        $form = tr_form($service_addon)->useErrors()->useOld()->useConfirm();
        return View::new('service_addons.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function update(ServiceAddon $service_addon, ServiceAddonFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_addon->can('update')) {
            $response->unauthorized('Unauthorized: ServiceAddon not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_addon->save($fields);

        return tr_redirect()->toPage('serviceaddon', 'edit', $service_addon->getID())
            ->withFlash('Service Addon updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function show(ServiceAddon $service_addon)
    {
        return $service_addon->with(['service', 'addonService', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function delete(ServiceAddon $service_addon)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceAddon $service_addon
     *
     * @return mixed
     */
    public function destroy(ServiceAddon $service_addon, Response $response)
    {
        if (!$service_addon->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceAddon not deleted');
        }

        $service_count = $service_addon->service()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service Addon(s) still use this.")
                ->setStatus(409)
                ->setData('service_addon', $service_addon);;
        }

        $deleted = $service_addon->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Addon deleted.')->setData('service_addon', $service_addon);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceAddon = ServiceAddon::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceAddon)) {
                return $response
                    ->setData('service_attribute_definition', [])
                    ->setMessage('No service Addons found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_attribute_definition', $serviceAddon)
                ->setMessage('Service Addon retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceAddon indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve service Addon: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
