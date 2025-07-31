<?php

namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceFields;
use MakerMaker\Models\Service;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

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
    public function add()
    {
        $form = tr_form(Service::class)->useErrors()->useOld();
        $button = 'Create Service';

        return View::new('services.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceFields $fields, Service $service, Response $response)
    {
        if (!$service->can('create')) {
            $response->unauthorized('Unauthorized: Service not created')->abort();
        }

        $service->save($fields);

        return tr_redirect()->toPage('service', 'index')
            ->withFlash('Service Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function edit(Service $service)
    {
        $form = tr_form($service)->useErrors()->useOld();
        $button = 'Update Service';

        return View::new('services.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function update(Service $service, ServiceFields $fields, Response $response)
    {
        if (!$service->can('update')) {
            $response->unauthorized('Unauthorized: Service not updated')->abort();
        }

        $service->save($fields);

        return tr_redirect()->toPage('service', 'edit', $service->getID())
            ->withFlash('Service Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function show(Service $service)
    {
        return $service;
    }

    /**
     * The delete page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function delete(Service $service)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function destroy(Service $service, Response $response)
    {
        if (!$service->can('destroy')) {
            $response->unauthorized('Unauthorized: Service not deleted')->abort();
        }

        $service->delete();

        return $response->warning('Service Deleted');
    }
}
