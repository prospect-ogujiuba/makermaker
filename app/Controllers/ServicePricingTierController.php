<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServicePricingTier;
use MakerMaker\Http\Fields\ServicePricingTierFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class ServicePricingTierController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_pricing_tiers.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServicePricingTier::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_pricing_tiers.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServicePricingTierFields $fields, ServicePricingTier $service_pricing_tier, Response $response, AuthUser $user)
    {
        if (!$service_pricing_tier->can('create')) {
            $response->unauthorized('Unauthorized: Service Pricing Tier not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');

        $fields['created_by'] = $user->ID;
        $fields['updated_by'] = $user->ID;

        $service_pricing_tier->save($fields);

        return tr_redirect()->toPage('servicepricingtier', 'index')
            ->withFlash('Service Pricing Tier created');
    }

    /**
     * The edit page for admin
     *
     * @param ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function edit(ServicePricingTier $service_pricing_tier, AuthUser $user)
    {
        $current_id = $service_pricing_tier->getID();
        $servicePrices = $service_pricing_tier->servicePrices;
        $createdBy = $service_pricing_tier->createdBy;
        $updatedBy = $service_pricing_tier->updatedBy;

        $form = tr_form($service_pricing_tier)->useErrors()->useOld()->useConfirm();
        return View::new('service_pricing_tiers.form', compact('form', 'current_id', 'servicePrices', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function update(ServicePricingTier $service_pricing_tier, ServicePricingTierFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_pricing_tier->can('update')) {
            $response->unauthorized('Unauthorized: Service Pricing Tier not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');

        $fields['updated_by'] = $user->ID;

        $service_pricing_tier->save($fields);

        return tr_redirect()->toPage('servicepricingtier', 'edit', $service_pricing_tier->getID())
            ->withFlash('Service Pricing Tier updated');
    }

    /**
     * The show page for admin
     *
     * @param ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function show(ServicePricingTier $service_pricing_tier)
    {
        return $service_pricing_tier->with(['servicePrices', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function delete(ServicePricingTier $service_pricing_tier)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function destroy(ServicePricingTier $service_pricing_tier, Response $response)
    {
        if (!$service_pricing_tier->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Pricing Tier not deleted');
        }

        // Check if this pricing tier is still being used by service prices
        $servicePricesCount = $service_pricing_tier->servicePrices()->count();

        if ($servicePricesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicePricesCount} service price(s) still use this pricing tier.")
                ->setStatus(409);
        }

        $deleted = $service_pricing_tier->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('ServicePricingTier deleted.')->setData('service_pricing_tier', $service_pricing_tier);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $servicePricingTiers = ServicePricingTier::new()
                ->with(['servicePrices', 'createdBy', 'updatedBy'])
                ->orderBy('sort_order', 'ASC')
                ->get();

            if (empty($servicePricingTiers)) {
                return $response
                    ->setData('service_pricing_tiers', [])
                    ->setMessage('No service Pricing Tiers found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_pricing_tiers', $servicePricingTiers)
                ->setMessage('Service Pricing Tiers retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServicePricingTier indexRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving service Pricing Tiers', 'error')
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServicePricingTier $service_pricing_tier
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServicePricingTier $service_pricing_tier, Response $response)
    {
        try {
            $service_pricing_tier = $service_pricing_tier->with(['servicePrices', 'createdBy', 'updatedBy'])->first();

            if (!$service_pricing_tier) {
                return $response
                    ->setMessage('Service Pricing Tier not found', 'error')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_pricing_tier', $service_pricing_tier)
                ->setMessage('Service Pricing Tier retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('ServicePricingTier showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving the Service Pricing Tier', 'error')
                ->setStatus(500);
        }
    }
}
