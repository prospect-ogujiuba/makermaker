<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServicePriceFields;
use MakerMaker\Models\PriceRecord;
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
     * Create item - Complete tracking version
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServicePriceFields $fields, ServicePrice $service_price, Response $response, AuthUser $user)
    {
        if (!$service_price->can('create')) {
            $response->unauthorized('Unauthorized: Service Price not created')->abort();
        }

        $service_price->created_by = $user->ID;
        $service_price->updated_by = $user->ID;

        $service_price->save($fields);

        // Capture ALL new values for compliance tracking
        $newData = [
            'amount' => $fields['amount'],
            'setup_fee' => $fields['setup_fee'],
            'currency' => $fields['currency'],
            'unit' => $fields['unit'],
            'valid_from' => $fields['valid_from'],
            'valid_to' => $fields['valid_to'],
            'is_current' => $fields['is_current'],
            'service_id' => $fields['service_id'],
            'pricing_tier_id' => $fields['pricing_tier_id'],
            'pricing_model_id' => $fields['pricing_model_id'],
            'approval_status' => $fields['approval_status'],
        ];

        PriceRecord::recordChange(
            $service_price->getID(),
            'created',
            [],  // No old data on creation
            $newData,
            'Initial price creation',
            $user->ID
        );

        return tr_redirect()->toPage('serviceprice', 'index')
            ->withFlash('Service Price created');
    }

    /**
     * The edit page for admin
     *
     * @param ServicePrice $service_price
     *
     * @return mixed
     */
    public function edit(ServicePrice $service_price, AuthUser $user)
    {
        $current_id = $service_price->getID();
        $service = $service_price->service;
        $pricingTier = $service_price->pricingTier;
        $pricingModel = $service_price->pricingModel;
        $createdBy = $service_price->createdBy;
        $updatedBy = $service_price->updatedBy;

        $form = tr_form($service_price)->useErrors()->useOld()->useConfirm();
        return View::new('service_prices.form', compact('form', 'current_id', 'service', 'pricingTier', 'pricingModel', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item - Complete tracking version
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePrice $service_price
     *
     * @return mixed
     */
    public function update(ServicePrice $service_price, ServicePriceFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_price->can('update')) {
            $response->unauthorized('Unauthorized: Service Price not updated')->abort();
        }

        // Capture ALL old values BEFORE update
        $oldData = [
            'amount' => $service_price->amount,
            'setup_fee' => $service_price->setup_fee,
            'currency' => $service_price->currency,
            'unit' => $service_price->unit,
            'valid_from' => $service_price->valid_from,
            'valid_to' => $service_price->valid_to,
            'is_current' => $service_price->is_current,
            'service_id' => $service_price->service_id,
            'pricing_tier_id' => $service_price->pricing_tier_id,
            'pricing_model_id' => $service_price->pricing_model_id,
            'approval_status' => $service_price->approval_status,
            'approved_by' => $service_price->approved_by,
            'approved_at' => $service_price->approved_at,
        ];

        $service_price->updated_by = $user->ID;

        $service_price->save($fields);

        // Refresh to get updated values
        $updated_service_price = ServicePrice::new()->findById($service_price->id);

        // Capture ALL new values AFTER update
        $newData = [
            'amount' => $updated_service_price->amount,
            'setup_fee' => $updated_service_price->setup_fee,
            'currency' => $updated_service_price->currency,
            'unit' => $updated_service_price->unit,
            'valid_from' => $updated_service_price->valid_from,
            'valid_to' => $updated_service_price->valid_to,
            'is_current' => $updated_service_price->is_current,
            'service_id' => $updated_service_price->service_id,
            'pricing_tier_id' => $updated_service_price->pricing_tier_id,
            'pricing_model_id' => $updated_service_price->pricing_model_id,
            'approval_status' => $updated_service_price->approval_status,
            'approved_by' => $updated_service_price->approved_by,
            'approved_at' => $updated_service_price->approved_at,
        ];

        PriceRecord::recordChange(
            $service_price->getID(),
            'updated',
            $oldData,
            $newData,
            'Price record updated',
            $user->ID
        );

        return tr_redirect()->toPage('serviceprice', 'edit', $service_price->getID())
            ->withFlash('Service Price updated');
    }

    /**
     * The show page for admin
     *
     * @param ServicePrice $service_price
     *
     * @return mixed
     */
    public function show(ServicePrice $service_price)
    {
        return $service_price;
    }

    /**
     * The delete page for admin
     *
     * @param ServicePrice $service_price
     *
     * @return mixed
     */
    public function delete(ServicePrice $service_price)
    {
        //
    }

    /**
     * Destroy item - With complete tracking
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePrice $service_price
     *
     * @return mixed
     */
    public function destroy(ServicePrice $service_price, Response $response, AuthUser $user)
    {
        if (!$service_price->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Price not deleted');
        }

        $service_count = $service_price->service()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_price->service->name} uses this price.")
                ->setStatus(409)
                ->setData('service_price', $service_price);
        }

        // Capture ALL values before deletion for compliance
        $oldData = [
            // Financial
            'amount' => $service_price->amount,
            'setup_fee' => $service_price->setup_fee,
            'currency' => $service_price->currency,
            'unit' => $service_price->unit,
            // Temporal
            'valid_from' => $service_price->valid_from,
            'valid_to' => $service_price->valid_to,
            'is_current' => $service_price->is_current,
            // Relationships
            'service_id' => $service_price->service_id,
            'pricing_tier_id' => $service_price->pricing_tier_id,
            'pricing_model_id' => $service_price->pricing_model_id,
            // Approval
            'approval_status' => $service_price->approval_status,
            'approved_by' => $service_price->approved_by,
            'approved_at' => $service_price->approved_at,
        ];

        $servicePriceId = $service_price->getID();

        $deleted = $service_price->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        // Record deletion with all previous values
        PriceRecord::recordChange(
            $servicePriceId,
            'deleted',
            $oldData,
            [],  // No new data on deletion
            'Price record deleted',
            $user->ID
        );

        return $response->success('Service Price deleted.')->setData('service_price', $service_price);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_prices = ServicePrice::new()->get();

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
            $service_price = ServicePrice::new()->find($service_price->getID());

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
