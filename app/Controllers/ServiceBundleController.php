<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceBundleFields;
use MakerMaker\Models\ServiceBundle;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceBundleController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_bundles.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceBundle::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_bundles.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceBundleFields $fields, ServiceBundle $service_bundle, Response $response, AuthUser $user)
    {
        if (!$service_bundle->can('create')) {
            $response->unauthorized('Unauthorized: Service bundle not created')->abort();
        }

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_bundle->save($fields);

        return tr_redirect()->toPage('servicebundle', 'index')
            ->withFlash('Service bundle created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function edit(ServiceBundle $service_bundle, AuthUser $user)
    {
        $current_id = $service_bundle->getID();
        $services = $service_bundle->services;
        $createdBy = $service_bundle->createdBy;
        $updatedBy = $service_bundle->updatedBy;

        $form = tr_form($service_bundle)->useErrors()->useOld()->useConfirm();
        return View::new('service_bundles.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function update(ServiceBundle $service_bundle, ServiceBundleFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_bundle->can('update')) {
            $response->unauthorized('Unauthorized: ServiceBundle not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $service_bundle->save($fields);

        return tr_redirect()->toPage('servicebundle', 'edit', $service_bundle->getID())
            ->withFlash('Service bundle updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function show(ServiceBundle $service_bundle)
    {
        return $service_bundle->with(['services', 'serviceType', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function delete(ServiceBundle $service_bundle)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceBundle $service_bundle
     *
     * @return mixed
     */
    public function destroy(ServiceBundle $service_bundle, Response $response)
    {
        if (!$service_bundle->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServiceBundle not deleted');
        }

        $servicesCount = $service_bundle->services()->count();

        if ($servicesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicesCount} service bundle(s) still use this. Reassign or remove them first.")
                ->setStatus(409);
        }

        $deleted = $service_bundle->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service bundle deleted.')->setData('service_pricing_model', $service_bundle);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $serviceBundle = ServiceBundle::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($serviceBundle)) {
                return $response
                    ->setData('service_bundle', [])
                    ->setMessage('No service bundles found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_bundle', $serviceBundle)
                ->setMessage('Service bundle retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServiceBundle indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve service bundle: ' . $e->getMessage())
                ->setStatus(500);
        }
    }
}
