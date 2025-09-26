<?php

// // Main Service resource
// $service = mm_create_custom_resource('Service', 'ServiceController', 'Services')->setIcon('cart')->setPosition(2);

// // Create all service subpages
// $serviceSubpages = [
//     mm_create_custom_resource('ComplexityLevel', 'ComplexityLevelController', 'Complexities'),
//     mm_create_custom_resource('PricingModel', 'PricingModelController', 'Pricing Models'),
//     mm_create_custom_resource('ServicePricingTier', 'ServicePricingTierController', 'Pricing Tiers'),
//     mm_create_custom_resource('ServiceDeliveryMethod', 'ServiceDeliveryMethodController', 'Delivery Methods'),
//     mm_create_custom_resource('ServiceCoverageArea', 'ServiceCoverageAreaController', 'Coverage Areas'),
//     mm_create_custom_resource('ServiceDeliverable', 'ServiceDeliverableController', 'Deliverables'),
//     mm_create_custom_resource('ServiceEquipment', 'ServiceEquipmentController', 'Equipment'),
//     mm_create_custom_resource('ServiceType', 'ServiceTypeController', 'Service Types'),
//     mm_create_custom_resource('ServiceCategory', 'ServiceCategoryController', 'Categories'),
//     mm_create_custom_resource('ServiceBundle', 'ServiceBundleController', 'Bundle Offers'),
//     mm_create_custom_resource('ServicePrice', 'ServicePriceController', 'Service Prices'),
//     mm_create_custom_resource('ServiceAddon', 'ServiceAddonController', 'Service Addons'),
//     mm_create_custom_resource('ServiceCoverage', 'ServiceCoverageController', 'Service Coverages'),
//     mm_create_custom_resource('ServiceDeliverableAssignment', 'ServiceDeliverableAssignmentController', 'Service Deliverables'),
//     mm_create_custom_resource('ServiceDeliveryMethodAssignment', 'ServiceDeliveryMethodAssignmentController', 'Delivery Assignments'),
//     mm_create_custom_resource('ServiceEquipmentAssignment', 'ServiceEquipmentAssignmentController', 'Service Equipment'),
//     mm_create_custom_resource('ServiceRelationship', 'ServiceRelationshipController', 'Service Relationships'),
//     mm_create_custom_resource('ServiceBundleItem', 'ServiceBundleItemController', 'Bundle Items'),
// ];

// Main Service resource
$service = mm_create_custom_resource('Service', 'ServiceController', 'Services')->setIcon('cart')->setPosition(2);

$serviceSubpages = [
    // Service Configuration & Taxonomy
    mm_create_custom_resource('ServiceCategory', 'ServiceCategoryController', 'Service Categories'),
    mm_create_custom_resource('ServiceType', 'ServiceTypeController', 'Service Types'),
    mm_create_custom_resource('ComplexityLevel', 'ComplexityLevelController', 'Complexity Levels'),

    // Pricing Management
    mm_create_custom_resource('PricingModel', 'PricingModelController', 'Pricing Models'),
    mm_create_custom_resource('PricingTier', 'PricingTierController', 'Pricing Tiers'),
    mm_create_custom_resource('ServicePrice', 'ServicePriceController', 'Service Pricing'),
    mm_create_custom_resource('CurrencyRate', 'CurrencyRateController', 'Currency Rates'),
    mm_create_custom_resource('PriceHistory', 'PriceHistoryController', 'Pricing History'),

    // Service Relationships & Configuration
    mm_create_custom_resource('ServiceAddon', 'ServiceAddonController', 'Service Add-ons'),
    mm_create_custom_resource('ServiceRelationship', 'ServiceRelationshipController', 'Service Dependencies'),

    // Service Delivery & Operations
    mm_create_custom_resource('DeliveryMethod', 'DeliveryMethodController', 'Delivery Methods'),
    mm_create_custom_resource('ServiceDelivery', 'ServiceDeliveryController', 'Service Delivery'),
    mm_create_custom_resource('CoverageArea', 'CoverageAreaController', 'Coverage Areas'),
    mm_create_custom_resource('ServiceCoverage', 'ServiceCoverageController', 'Service Coverage'),

    // Resources & Assets
    mm_create_custom_resource('Equipment', 'EquipmentController', 'Equipment Catalog'),
    mm_create_custom_resource('ServiceEquipment', 'ServiceEquipmentController', 'Service Equipment'),
    mm_create_custom_resource('Deliverable', 'DeliverableController', 'Deliverables'),
    mm_create_custom_resource('ServiceDeliverable', 'ServiceDeliverableController', 'Service Deliverables'),

    // Package & Bundle Management
    mm_create_custom_resource('ServiceBundle', 'ServiceBundleController', 'Service Bundles'),
    mm_create_custom_resource('BundleItem', 'BundleItemController', 'Bundle Components'),
];

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}
