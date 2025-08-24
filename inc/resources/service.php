<?php

// Main Service resource
$service = tr_resource_pages('Service@\MakerMaker\Controllers\Web\ServiceController', 'Services')
    ->setIcon('cart')
    ->setPosition(2);

// Helper function to create service resources
function createServiceResource($resourceKey, $controller, $title, $hasAddButton = false) {
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
    createServiceResource('ServiceAddons', 'ServiceAddonController', 'Service Addons', true),
    createServiceResource('ServiceBundles', 'ServiceBundleController', 'Service Bundles', true),
    createServiceResource('ServiceCategory', 'ServiceCategoryController', 'Service Categories', true),
    createServiceResource('ServiceComplexity', 'ServiceComplexityController', 'Service Complexity'),
    createServiceResource('ServiceCoverageArea', 'ServiceCoverageAreaController', 'Coverage Areas'),
    createServiceResource('ServiceDeliverables', 'ServiceDeliverableController', 'Deliverables'),
    createServiceResource('ServiceDeliveryMethod', 'ServiceDeliveryMethodController', 'Delivery Method'),
    createServiceResource('ServiceDependency', 'ServiceDependencyController', 'Dependencies'),
    createServiceResource('ServiceEquipment', 'ServiceEquipmentController', 'Service Equipment'),
    createServiceResource('ServicePrerequisite', 'ServicePrerequisiteController', 'Prerequisites'),
    createServiceResource('ServicePricingTier', 'ServicePricingTierController', 'Pricing Tiers'),
    createServiceResource('ServicePricingModel', 'ServicePricingModelController', 'Pricing Models'),
    createServiceResource('ServiceType', 'ServiceTypeController', 'Service Types')
];

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}

