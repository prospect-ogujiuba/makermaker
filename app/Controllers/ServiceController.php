<?php
namespace MakerMaker\Controllers;

use MakerMaker\Models\Service;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Request;
use TypeRocket\Http\Response;

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
        $form = tr_form(Service::class);
        $button = 'Add';

        return View::new('services.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(Request $request, Service $service)
    {
        $service->save($request->fields());

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
        $form = tr_form($service);
        $button = 'Update';

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
    public function update(Service $service, Request $request)
    {
        $service->save($request->fields());

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
        return View::new('services.show', compact('service'));
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
        return View::new('services.delete', compact('service'));
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
        if (!$service->can('delete')) {
            $response->unauthorized('Unauthorized: Service not deleted')->abort();
        }

        $service->delete();

        return $response->warning('Service Deleted');
    }

    /**
     * Get active services for API/AJAX requests
     *
     * @return mixed
     */
    public function active(Response $response)
    {
        $services = Service::active()->get();
        
        return $response->json($services);
    }

    /**
     * Get services by category for API/AJAX requests
     *
     * @param Request $request
     * @return mixed
     */
    public function byCategory(Request $request, Response $response)
    {
        $category = $request->input('category');
        
        if (!$category) {
            return $response->json(['error' => 'Category parameter required'], 400);
        }

        $services = Service::active()->byCategory($category)->get();
        
        return $response->json($services);
    }
}