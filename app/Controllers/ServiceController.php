<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceFields;
use MakerMaker\Models\Service;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('services.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(Service::class)->useErrors()->useOld()->useConfirm();
        return View::new('services.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceFields $fields, Service $service, Response $response, AuthUser $user)
    {
        if (!$service->can('create')) {
            $response->unauthorized('Unauthorized: Service not created')->abort();
        }

        autoGenerateCode($fields, 'sku', 'name', '-', NULL, NULL, true);
        autoGenerateCode($fields, 'slug', 'name', '-');

        $service->created_by = $user->ID;
        $service->updated_by = $user->ID;

        $service->save($fields);

        return tr_redirect()->toPage('service', 'index')
            ->withFlash('Service created');
    }

    /**
     * The edit page for admin
     *
     * @param Service $service
     *
     * @return mixed
     */
    public function edit(Service $service, AuthUser $user)
    {
        $current_id = $service->getID();
        $createdBy = $service->createdBy;
        $updatedBy = $service->updatedBy;

        $form = tr_form($service)->useErrors()->useOld()->useConfirm();
        return View::new('services.form', compact('form', 'current_id', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param Service $service
     *
     * @return mixed
     */
    public function update(Service $service, ServiceFields $fields, Response $response, AuthUser $user)
    {
        if (!$service->can('update')) {
            $response->unauthorized('Unauthorized: Service not updated')->abort();
        }

        autoGenerateCode($fields, 'sku', 'name', '-', NULL, 'NULL', true);
        autoGenerateCode($fields, 'slug', 'name', '-');

        $service->updated_by = $user->ID;

        $service->save($fields);

        return tr_redirect()->toPage('service', 'edit', $service->getID())
            ->withFlash('Service updated');
    }

    /**
     * The show page for admin
     *
     * @param Service $service
     *
     * @return mixed
     */
    public function show(Service $service)
    {
        return $service->with(['createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param Service $service
     *
     * @return mixed
     */
    public function delete(Service $service)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param Service $service
     *
     * @return mixed
     */
    public function destroy(Service $service, Response $response)
    {
        if (!$service->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service not deleted');
        }

        $service_count = $service->serviceType()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this.")
                ->setStatus(409)
                ->setData('service', $service);
        }

        $deleted = $service->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service deleted.')->setData('service', $service);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $services = Service::new()
                ->with(['createdBy', 'updatedBy'])
                ->get();

            if (empty($services)) {
                return $response
                    ->setData('services', [])
                    ->setMessage('No Services found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('services', $services)
                ->setMessage('Services retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Services: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param Service $service
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(Service $service, Response $response)
    {
        try {
            $service = Service::new()
                ->with(['createdBy', 'updatedBy'])
                ->find($service->getID());

            if (empty($service)) {
                return $response
                    ->setData('service', null)
                    ->setMessage('Service not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service', $service)
                ->setMessage('Service retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service', 'error')
                ->setStatus(500);
        }
    }
}
