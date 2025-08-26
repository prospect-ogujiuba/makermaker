<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ServicePricingTier;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;

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
    public function add()
    {
        // TODO: Implement add() method.
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create()
    {
        // TODO: Implement create() method.
    }

    /**
     * The edit page for admin
     *
     * @param string|ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function edit(ServicePricingTier $service_pricing_tier)
    {
        // TODO: Implement edit() method.
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function update(ServicePricingTier $service_pricing_tier)
    {
        // TODO: Implement update() method.
    }

    /**
     * The show page for admin
     *
     * @param string|ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function show(ServicePricingTier $service_pricing_tier)
    {
        // TODO: Implement show() method.
    }

    /**
     * The delete page for admin
     *
     * @param string|ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function delete(ServicePricingTier $service_pricing_tier)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServicePricingTier $service_pricing_tier
     *
     * @return mixed
     */
    public function destroy(ServicePricingTier $service_pricing_tier)
    {
        // TODO: Implement destroy() method.
    }
}
