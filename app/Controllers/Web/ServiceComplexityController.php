<?php

namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceComplexityFields;
use MakerMaker\Models\ServiceComplexity;
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
        return View::new('service_complexity.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(ServiceComplexity::class)->useErrors()->useOld();
        $button = 'Create Complexity Level';

        return View::new('service_complexity.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceComplexityFields $fields, ServiceComplexity $complexity, Response $response)
    {
        if (!$complexity->can('create')) {
            $response->unauthorized('Unauthorized: Service Complexity not created')->abort();
        }

        $complexity->save($fields);

        return tr_redirect()->toPage('servicecomplexity', 'index')
            ->withFlash('Complexity Level Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceComplexity $complexity
     *
     * @return mixed
     */
    public function edit(ServiceComplexity $complexity)
    {
        $form = tr_form($complexity)->useErrors()->useOld();
        $button = 'Update Complexity Level';

        return View::new('service_complexity.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceComplexity $complexity
     *
     * @return mixed
     */
    public function update(ServiceComplexity $complexity, ServiceComplexityFields $fields, Response $response)
    {
        if (!$complexity->can('update')) {
            $response->unauthorized('Unauthorized: Service Complexity not updated')->abort();
        }

        $complexity->save($fields);

        return tr_redirect()->toPage('servicecomplexity', 'edit', $complexity->getID())
            ->withFlash('Complexity Level Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceComplexity $complexity
     *
     * @return mixed
     */
    public function show(ServiceComplexity $complexity)
    {
        return $complexity;
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceComplexity $complexity
     *
     * @return mixed
     */
    public function delete(ServiceComplexity $complexity)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceComplexity $complexity
     *
     * @return mixed
     */
    public function destroy(ServiceComplexity $complexity, Response $response)
    {
        if (!$complexity->can('destroy')) {
            $response->unauthorized('Unauthorized: Service Complexity not deleted')->abort();
        }

        // Check if complexity level can be safely deleted
        if (!$complexity->canBeDeleted()) {
            if ($complexity->isInUse()) {
                return $response->error('Cannot delete complexity level: It is being used by ' . 
                    $complexity->getServicesCount() . ' service(s). Please reassign those services first.');
            } else {
                return $response->error('Cannot delete core complexity level: This is a system-required complexity level.');
            }
        }

        $complexity->delete();

        return $response->warning('Service Complexity Deleted');
    }
}