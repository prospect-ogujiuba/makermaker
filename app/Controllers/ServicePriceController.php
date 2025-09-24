<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServicePriceFields;
use MakerMaker\Models\ServicePrice;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServicePriceController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_prices.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServicePrice::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_prices.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServicePriceFields $fields, ServicePrice $servicePrice, Response $response, AuthUser $user)
    {
        if (!$servicePrice->can('create')) {
            $response->unauthorized('Unauthorized: Service Price not created')->abort();
        }

        $servicePrice->created_by = $user->ID;
        $servicePrice->updated_by = $user->ID;

        $servicePrice->save($fields);

        return tr_redirect()->toPage('serviceprice', 'index')
            ->withFlash('Service Price created');
    }

    /**
     * The edit page for admin
     *
     * @param ServicePrice $servicePrice
     *
     * @return mixed
     */
    public function edit(ServicePrice $servicePrice, AuthUser $user)
    {
        $current_id = $servicePrice->getID();
        $service = $servicePrice->service;
        $pricingTier = $servicePrice->pricingTier;
        $pricingModel = $servicePrice->pricingModel;
        $createdBy = $servicePrice->createdBy;
        $updatedBy = $servicePrice->updatedBy;

        $form = tr_form($servicePrice)->useErrors()->useOld()->useConfirm();
        return View::new('service_prices.form', compact('form', 'current_id', 'service', 'pricingTier', 'pricingModel', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePrice $servicePrice
     *
     * @return mixed
     */
    public function update(ServicePrice $servicePrice, ServicePriceFields $fields, Response $response, AuthUser $user)
    {
        if (!$servicePrice->can('update')) {
            $response->unauthorized('Unauthorized: Service Price not updated')->abort();
        }

        $fields['updated_by'] = $user->ID;

        $servicePrice->save($fields);

        return tr_redirect()->toPage('serviceprice', 'edit', $servicePrice->getID())
            ->withFlash('Service Price updated');
    }

    /**
     * The show page for admin
     *
     * @param ServicePrice $servicePrice
     *
     * @return mixed
     */
    public function show(ServicePrice $servicePrice)
    {
        return $servicePrice->with(['service', 'pricingTier', 'pricingModel', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServicePrice $servicePrice
     *
     * @return mixed
     */
    public function delete(ServicePrice $servicePrice)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePrice $servicePrice
     *
     * @return mixed
     */
    public function destroy(ServicePrice $servicePrice, Response $response)
    {
        if (!$servicePrice->can('destroy')) {
            return $response->unauthorized('Unauthorized: ServicePrice not deleted');
        }

        $deleted = $servicePrice->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Price deleted.')->setData('serviceprice', $servicePrice);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_prices = ServicePrice::new()
                ->with(['service', 'pricingTier', 'pricingModel', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_prices)) {
                return $response
                    ->setData('service_prices', [])
                    ->setMessage('No Service Prices found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_prices', $service_prices)
                ->setMessage('Service Prices retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Price indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Prices: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServicePrice $service_price
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServicePrice $service_price, Response $response)
    {
        try {
            $service_price = ServicePrice::new()
                ->with(['service', 'pricingTier', 'pricingModel', 'createdBy', 'updatedBy'])
                ->find($service_price->getID());

            if (empty($service_price)) {
                return $response
                    ->setData('service_price', null)
                    ->setMessage('Service Price not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_price', $service_price)
                ->setMessage('Service Price retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Price showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Price', 'error')
                ->setStatus(500);
        }
    }
}
