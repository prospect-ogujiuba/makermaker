<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServiceComplexity;
use MakerMaker\Http\Fields\ServiceComplexityFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServiceComplexityController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_complexities.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(ServiceComplexity::class)->useErrors()->useOld();;
        $button = 'Add';

        return View::new('service_complexities.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceComplexityFields $fields, ServiceComplexity $info_session, Response $response)
    {

        if (!$info_session->can('create')) {
            $response->unauthorized('Unauthorized: ServiceComplexity not created')->abort();
        }

        $info_session->save($fields);

        return tr_redirect()->toPage('serviceComplexity', 'index')
            ->withFlash('Info Session Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceComplexity $info_session
     *
     * @return mixed
     */
    public function edit(ServiceComplexity $info_session)
    {
        $form = tr_form($info_session)->useErrors()->useOld();;
        $button = 'Update';

        return View::new('service_complexities.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceComplexity $info_session
     *
     * @return mixed
     */
    public function update(ServiceComplexity $info_session, ServiceComplexityFields $fields, Response $response)
    {

        if (!$info_session->can('update')) {
            $response->unauthorized('Unauthorized: ServiceComplexity not updated')->abort();
        }

        $info_session->save($fields);

        return tr_redirect()->toPage('serviceComplexity', 'edit', $info_session->getID())
            ->withFlash('Info Session Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceComplexity $info_session
     *
     * @return mixed
     */
    public function show(ServiceComplexity $info_session)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceComplexity $info_session
     *
     * @return mixed
     */
    public function delete(ServiceComplexity $info_session)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceComplexity $info_session
     *
     * @return mixed
     */
    public function destroy(ServiceComplexity $info_session, Response $response)
    {
        if (!$info_session->can('destroy')) {
            $response->unauthorized('Unauthorized: ServiceComplexity not deleted')->abort();
        }

        $info_session->delete();

        return $response->warning('ServiceComplexity Deleted');
    }
}
