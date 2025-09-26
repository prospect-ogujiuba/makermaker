<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\PricingModel;
use MakerMaker\Http\Fields\PricingModelFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class PricingModelController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('pricing_models.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(PricingModel::class)->useErrors()->useOld()->useConfirm();
        return View::new('pricing_models.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(PricingModelFields $fields, PricingModel $pricing_model, Response $response, AuthUser $user)
    {
        if (!$pricing_model->can('create')) {
            $response->unauthorized('Unauthorized: Pricing Model not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $pricing_model->created_by = $user->ID;
        $pricing_model->updated_by = $user->ID;

        $pricing_model->save($fields);

        return tr_redirect()->toPage('pricingmodel', 'index')
            ->withFlash('Pricing Model created');
    }

    /**
     * The edit page for admin
     *
     * @param PricingModel $pricing_model
     *
     * @return mixed
     */
    public function edit(PricingModel $pricing_model, AuthUser $user)
    {
        $current_id = $pricing_model->getID();
        $servicePrices = $pricing_model->servicePrices;
        $createdBy = $pricing_model->createdBy;
        $updatedBy = $pricing_model->updatedBy;

        $form = tr_form($pricing_model)->useErrors()->useOld()->useConfirm();
        return View::new('pricing_models.form', compact('form', 'current_id', 'servicePrices', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param PricingModel $pricing_model
     *
     * @return mixed
     */
    public function update(PricingModel $pricing_model, PricingModelFields $fields, Response $response, AuthUser $user)
    {
        if (!$pricing_model->can('update')) {
            $response->unauthorized('Unauthorized: Pricing Model not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $pricing_model->updated_by = $user->ID;

        $pricing_model->save($fields);

        return tr_redirect()->toPage('pricingmodel', 'edit', $pricing_model->getID())
            ->withFlash('Pricing Model updated');
    }

    /**
     * The show page for admin
     *
     * @param PricingModel $pricing_model
     *
     * @return mixed
     */
    public function show(PricingModel $pricing_model)
    {
        return $pricing_model;
    }

    /**
     * The delete page for admin
     *
     * @param PricingModel $pricing_model
     *
     * @return mixed
     */
    public function delete(PricingModel $pricing_model)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param PricingModel $pricing_model
     *
     * @return mixed
     */
    public function destroy(PricingModel $pricing_model, Response $response)
    {
        if (!$pricing_model->can('destroy')) {
            return $response->unauthorized('Unauthorized: Pricing Model not deleted');
        }

        $servicePricesCount = $pricing_model->servicePrices()->count();

        if ($servicePricesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicePricesCount} Service Price(s) still use this pricing model.")
                ->setStatus(409)
                ->setData('pricing_model', $pricing_model);
        }

        $deleted = $pricing_model->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Pricing Model deleted.')->setData('pricing_model', $pricing_model);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $pricing_models = PricingModel::new()
                ->get();

            if (empty($pricing_models)) {
                return $response
                    ->setData('pricing_models', [])
                    ->setMessage('No Pricing Models found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('pricing_models', $pricing_models)
                ->setMessage('Pricing Models retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Pricing Model indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Pricing Models: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param PricingModel $pricing_model
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(PricingModel $pricing_model, Response $response)
    {
        try {
            $pricing_model = PricingModel::new()
                ->find($pricing_model->getID());

            if (empty($pricing_model)) {
                return $response
                    ->setData('pricing_model', null)
                    ->setMessage('Pricing Model not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('pricing_model', $pricing_model)
                ->setMessage('Pricing Model retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Pricing Model showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Pricing Model', 'error')
                ->setStatus(500);
        }
    }
}
