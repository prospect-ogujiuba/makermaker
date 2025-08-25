<?php

// Main Service resource
$service = tr_resource_pages('Service@\MakerMaker\Controllers\Web\ServiceController', 'Services')
    ->setIcon('cart')
    ->setPosition(2);

// Helper function to create service resources
function createServiceResource($resourceKey, $controller, $title, $hasAddButton = true)
{
    $resourcePage = tr_resource_pages(
        $resourceKey . '@\MakerMaker\Controllers\Web\\' . $controller,
        $title
    );

    if ($hasAddButton) {
        $adminPageSlug = strtolower($resourceKey) . '_add';
        $resourcePage->addNewButton(admin_url('admin.php?page=' . $adminPageSlug));
    }

    return $resourcePage;
}

// Create all service subpages
$serviceSubpages = [
    createServiceResource('ServiceAddons', 'ServiceAddonController', 'Addons'),
    createServiceResource('ServiceAttributes', 'ServiceAttributeController', 'Attributes'),
    createServiceResource('ServiceBundle', 'ServiceBundleController', 'Bundles'),
    createServiceResource('ServiceCategory', 'ServiceCategoryController', 'Categories'),
    createServiceResource('ServiceComplexity', 'ServiceComplexityController', 'Complexity'),
    createServiceResource('ServiceCoverageArea', 'ServiceCoverageAreaController', 'Coverage Areas'),
    createServiceResource('ServiceDeliverables', 'ServiceDeliverableController', 'Deliverables'),
    createServiceResource('ServiceDeliveryMethod', 'ServiceDeliveryMethodController', 'Delivery Method'),
    createServiceResource('ServiceDependency', 'ServiceDependencyController', 'Dependencies'),
    createServiceResource('ServiceEquipment', 'ServiceEquipmentController', 'Equipment'),
    createServiceResource('ServicePrerequisite', 'ServicePrerequisiteController', 'Prerequisites'),
    createServiceResource('ServicePricingTier', 'ServicePricingTierController', 'Pricing Tiers'),
    createServiceResource('ServicePricingModel', 'ServicePricingModelController', 'Pricing Models'),
    createServiceResource('ServiceType', 'ServiceTypeController', 'Types')
];

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}
