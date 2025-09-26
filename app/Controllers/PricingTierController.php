<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\PricingTier;
use MakerMaker\Http\Fields\PricingTierFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class PricingTierController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('pricing_tiers.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(PricingTier::class)->useErrors()->useOld()->useConfirm();
        return View::new('pricing_tiers.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(PricingTierFields $fields, PricingTier $pricing_tier, Response $response, AuthUser $user)
    {
        if (!$pricing_tier->can('create')) {
            $response->unauthorized('Unauthorized: Pricing Tier not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $pricing_tier->created_by = $user->ID;
        $pricing_tier->updated_by = $user->ID;

        $pricing_tier->save($fields);

        return tr_redirect()->toPage('PricingTier', 'index')
            ->withFlash('Pricing Tier created');
    }

    /**
     * The edit page for admin
     *
     * @param PricingTier $pricing_tier
     *
     * @return mixed
     */
    public function edit(PricingTier $pricing_tier, AuthUser $user)
    {
        $current_id = $pricing_tier->getID();
        $servicePrices = $pricing_tier->servicePrices;
        $createdBy = $pricing_tier->createdBy;
        $updatedBy = $pricing_tier->updatedBy;

        $form = tr_form($pricing_tier)->useErrors()->useOld()->useConfirm();
        return View::new('pricing_tiers.form', compact('form', 'current_id', 'servicePrices', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param PricingTier $pricing_tier
     *
     * @return mixed
     */
    public function update(PricingTier $pricing_tier, PricingTierFields $fields, Response $response, AuthUser $user)
    {
        if (!$pricing_tier->can('update')) {
            $response->unauthorized('Unauthorized: Pricing Tier not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $pricing_tier->updated_by = $user->ID;

        $pricing_tier->save($fields);

        return tr_redirect()->toPage('PricingTier', 'edit', $pricing_tier->getID())
            ->withFlash('Pricing Tier updated');
    }

    /**
     * The show page for admin
     *
     * @param PricingTier $pricing_tier
     *
     * @return mixed
     */
    public function show(PricingTier $pricing_tier)
    {
        return $pricing_tier;
    }

    /**
     * The delete page for admin
     *
     * @param PricingTier $pricing_tier
     *
     * @return mixed
     */
    public function delete(PricingTier $pricing_tier)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param PricingTier $pricing_tier
     *
     * @return mixed
     */
    public function destroy(PricingTier $pricing_tier, Response $response)
    {
        if (!$pricing_tier->can('destroy')) {
            return $response->unauthorized('Unauthorized: Pricing Tier not deleted');
        }

        // Check if this pricing tier is still being used by service prices
        $servicePricesCount = $pricing_tier->servicePrices()->count();

        if ($servicePricesCount > 0) {
            return $response
                ->error("Cannot delete: {$servicePricesCount} service price(s) still use this pricing tier.")
                ->setStatus(409)
                ->setData('pricing_tier', $pricing_tier);
        }

        $deleted = $pricing_tier->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Pricing Tier deleted.')->setData('pricing_tier', $pricing_tier);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $pricing_tiers = PricingTier::new()
                ->orderBy('sort_order', 'ASC')
                ->get();

            if (empty($pricing_tiers)) {
                return $response
                    ->setData('pricing_tiers', [])
                    ->setMessage('No Pricing Tiers found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('pricing_tiers', $pricing_tiers)
                ->setMessage('Pricing Tiers retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Pricing Tier indexRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Pricing Tiers', 'error')
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param PricingTier $pricing_tier
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(PricingTier $pricing_tier, Response $response)
    {
        try {
            $pricing_tier = $pricing_tier->first();

            if (!$pricing_tier) {
                return $response
                    ->setMessage('Pricing Tier not found', 'error')
                    ->setStatus(404);
            }

            return $response
                ->setData('pricing_tier', $pricing_tier)
                ->setMessage('Pricing Tier retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Pricing Tier showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving the Pricing Tier', 'error')
                ->setStatus(500);
        }
    }
}
