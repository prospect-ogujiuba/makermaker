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
            $response->unauthorized('Unauthorized: Service Bundle not created')->abort();
        }

        autoGenerateCode($fields, 'slug', 'name', '-');

        $service_bundle->created_by = $user->ID;
        $service_bundle->updated_by = $user->ID;

        $service_bundle->save($fields);

        return tr_redirect()->toPage('servicebundle', 'index')
            ->withFlash('Service Bundle created');
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
            $response->unauthorized('Unauthorized: Service Bundle not updated')->abort();
        }

        autoGenerateCode($fields, 'slug', 'name', '-');

        $service_bundle->updated_by = $user->ID;

        $service_bundle->save($fields);

        return tr_redirect()->toPage('servicebundle', 'edit', $service_bundle->getID())
            ->withFlash('Service Bundle updated');
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
        return $service_bundle;
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
            return $response->unauthorized('Unauthorized: Service Bundle not deleted');
        }

        $service_count = $service_bundle->services()->count('service_id');

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} Service Bundle(s) still use this.")
                ->setStatus(409)
                ->setData('service_bundle', $service_bundle);
        }

        $deleted = $service_bundle->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Bundle deleted.')->setData('service_bundle', $service_bundle);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_bundles = ServiceBundle::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_bundles)) {
                return $response
                    ->setData('service_bundles', [])
                    ->setMessage('No Service Bundles found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_bundles', $service_bundles)
                ->setMessage('Service Bundles retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Bundle indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Bundles: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceBundle $service_bundle
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceBundle $service_bundle, Response $response)
    {
        try {
            $service_bundle = ServiceBundle::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($service_bundle->getID());

            if (empty($service_bundle)) {
                return $response
                    ->setData('service_bundle', null)
                    ->setMessage('Service Bundle not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_bundle', $service_bundle)
                ->setMessage('Service Bundle retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Bundle showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Bundle', 'error')
                ->setStatus(500);
        }
    }
}
