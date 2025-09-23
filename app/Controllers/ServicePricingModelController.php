<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServicePricingModel;
use MakerMaker\Http\Fields\ServicePricingModelFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

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
    public function add(AuthUser $user)
    {
        $form = tr_form(ServicePricingModel::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_pricing_models.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServicePricingModelFields $fields, ServicePricingModel $service_pricing_model, Response $response, AuthUser $user)
    {
        if (!$service_pricing_model->can('create')) {
            $response->unauthorized('Unauthorized: Service Pricing Model not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_pricing_model->save($fields);

        return tr_redirect()->toPage('servicepricingmodel', 'index')
            ->withFlash('Service Pricing Model created');
    }

    /**
     * The edit page for admin
     *
     * @param ServicePricingModel $service_pricing_model
     *
     * @return mixed
     */
    public function edit(ServicePricingModel $service_pricing_model, AuthUser $user)
    {
        $current_id = $service_pricing_model->getID();
        $servicePrices = $service_pricing_model->servicePrices;
        $createdBy = $service_pricing_model->createdBy;
        $updatedBy = $service_pricing_model->updatedBy;

        $form = tr_form($service_pricing_model)->useErrors()->useOld()->useConfirm();
        return View::new('service_pricing_models.form', compact('form', 'current_id', 'servicePrices', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePricingModel $service_pricing_model
     *
     * @return mixed
     */
    public function update(ServicePricingModel $service_pricing_model, ServicePricingModelFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_pricing_model->can('update')) {
            $response->unauthorized('Unauthorized: Service Pricing Model not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $fields['updated_by'] = $user->ID;

        $service_pricing_model->save($fields);

        return tr_redirect()->toPage('servicepricingmodel', 'edit', $service_pricing_model->getID())
            ->withFlash('Service Pricing Model updated');
    }

    /**
     * The show page for admin
     *
     * @param ServicePricingModel $service_pricing_model
     *
     * @return mixed
     */
    public function show(ServicePricingModel $service_pricing_model)
    {
        return $service_pricing_model->with(['servicePrices', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServicePricingModel $service_pricing_model
     *
     * @return mixed
     */
    public function delete(ServicePricingModel $service_pricing_model)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePricingModel $service_pricing_model
     *
     * @return mixed
     */
    public function destroy(ServicePricingModel $service_pricing_model, Response $response)
    {
        if (!$service_pricing_model->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Pricing Model not deleted');
        }

        $servicePricesCount = $service_pricing_model->servicePrices()->count();

        if ($servicePricesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicePricesCount} Service Price(s) still use this pricing model.")
                ->setStatus(409)
                ->setData('service_pricing_model', $service_pricing_model);
        }

        $deleted = $service_pricing_model->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Pricing Model deleted.')->setData('service_pricing_model', $service_pricing_model);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_pricing_models = ServicePricingModel::new()
                ->with(['servicePrices', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_pricing_models)) {
                return $response
                    ->setData('service_pricing_models', [])
                    ->setMessage('No Service Pricing Models found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_pricing_models', $service_pricing_models)
                ->setMessage('Service Pricing Models retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Pricing Model indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Pricing Models: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServicePricingModel $service_pricing_model
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServicePricingModel $service_pricing_model, Response $response)
    {
        try {
            $service_pricing_model = ServicePricingModel::new()
                ->with(['servicePrices', 'createdBy', 'updatedBy'])
                ->find($service_pricing_model->getID());

            if (empty($service_pricing_model)) {
                return $response
                    ->setData('service_pricing_model', null)
                    ->setMessage('Service Pricing Model not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_pricing_model', $service_pricing_model)
                ->setMessage('Service Pricing Model retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Pricing Model showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Pricing Model', 'error')
                ->setStatus(500);
        }
    }
}
