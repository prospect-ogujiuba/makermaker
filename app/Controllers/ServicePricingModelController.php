<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServicePricingModel;
use MakerMaker\Http\Fields\ServicePricingModelFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServicePricingModelController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_pricing_models.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(ServicePricingModel::class)->useErrors()->useOld();;
        $button = 'Add';

        return View::new('service_pricing_models.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServicePricingModelFields $fields, ServicePricingModel $info_session, Response $response)
    {

        if (!$info_session->can('create')) {
            $response->unauthorized('Unauthorized: ServicePricingModel not created')->abort();
        }

        $info_session->save($fields);

        return tr_redirect()->toPage('servicePricingModel', 'index')
            ->withFlash('Info Session Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServicePricingModel $info_session
     *
     * @return mixed
     */
    public function edit(ServicePricingModel $info_session)
    {
        $form = tr_form($info_session)->useErrors()->useOld();;
        $button = 'Update';

        return View::new('service_pricing_model.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServicePricingModel $info_session
     *
     * @return mixed
     */
    public function update(ServicePricingModel $info_session, ServicePricingModelFields $fields, Response $response)
    {

        if (!$info_session->can('update')) {
            $response->unauthorized('Unauthorized: ServicePricingModel not updated')->abort();
        }

        $info_session->save($fields);

        return tr_redirect()->toPage('servicePricingModel', 'edit', $info_session->getID())
            ->withFlash('Info Session Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServicePricingModel $info_session
     *
     * @return mixed
     */
    public function show(ServicePricingModel $info_session)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServicePricingModel $info_session
     *
     * @return mixed
     */
    public function delete(ServicePricingModel $info_session)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServicePricingModel $info_session
     *
     * @return mixed
     */
    public function destroy(ServicePricingModel $info_session, Response $response)
    {
        if (!$info_session->can('destroy')) {
            $response->unauthorized('Unauthorized: ServicePricingModel not deleted')->abort();
        }

        $info_session->delete();

        return $response->warning('ServicePricingModel Deleted');
    }
}
