<?php

namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceAddonFields;
use MakerMaker\Models\ServiceAddon;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

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
    public function add()
    {
        $form = tr_form(ServiceAddon::class)->useErrors()->useOld();
        $button = 'Create Service Addon';

        return View::new('service_addons.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceAddonFields $fields, ServiceAddon $serviceAddon, Response $response)
    {
        if (!$serviceAddon->can('create')) {
            $response->unauthorized('Unauthorized: Service Addon not created')->abort();
        }

        $serviceAddon->save($fields);

        return tr_redirect()->toPage('serviceaddon', 'index')
            ->withFlash('Service Addon Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceAddon $serviceAddon
     *
     * @return mixed
     */
    public function edit(ServiceAddon $serviceAddon)
    {
        $form = tr_form($serviceAddon)->useErrors()->useOld();
        $button = 'Update Service Addon';

        return View::new('service_addons.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAddon $serviceAddon
     *
     * @return mixed
     */
    public function update(ServiceAddon $serviceAddon, ServiceAddonFields $fields, Response $response)
    {
        if (!$serviceAddon->can('update')) {
            $response->unauthorized('Unauthorized: Service Addon not updated')->abort();
        }

        $serviceAddon->save($fields);

        return tr_redirect()->toPage('serviceaddon', 'edit', $serviceAddon->getID())
            ->withFlash('Service Addon Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceAddon $serviceAddon
     *
     * @return mixed
     */
    public function show(ServiceAddon $serviceAddon)
    {
        return $serviceAddon;
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceAddon $serviceAddon
     *
     * @return mixed
     */
    public function delete(ServiceAddon $serviceAddon)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceAddon $serviceAddon
     *
     * @return mixed
     */
    public function destroy(ServiceAddon $serviceAddon, Response $response)
    {
        if (!$serviceAddon->can('destroy')) {
            $response->unauthorized('Unauthorized: Service Addon not deleted')->abort();
        }

        $serviceAddon->delete();

        return $response->warning('Service Addon Deleted');
    }
}