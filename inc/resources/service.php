<?php


// // ============================================================================
// // SERVICE MAIN RESOURCE
// // ============================================================================

// $service = mm_create_custom_resource('Service', 'ServiceController', 'Services')
//     ->setIcon('cart')
//     ->setPosition(2);


// // ============================================================================
// // CATEGORIES & TAXONOMY
// // ============================================================================

// $service->addPage(mm_create_custom_resource('ServiceCategory', 'ServiceCategoryController', 'Categories'));


// $service->addPage(
//     mm_create_custom_resource('ServiceType', 'ServiceTypeController', 'Service Types')
// );
// $service->addPage(
//     mm_create_custom_resource('ComplexityLevel', 'ComplexityLevelController', 'Complexity Levels')
// );
// $service->addPage(
//     mm_create_custom_resource('ServiceRelationship', 'ServiceRelationshipController', 'Dependencies')
// );
// $service->addPage(
//     mm_create_custom_resource('ServiceAddon', 'ServiceAddonController', 'Add-ons')
// );
// $service->addPage(
//     mm_create_custom_resource('ServiceBundle', 'ServiceBundleController', 'Bundles')
// );
// $service->addPage(
//     mm_create_custom_resource('BundleItem', 'BundleItemController', 'Bundle Items')
// );


// // ============================================================================
// // PRICING MANAGEMENT
// // ============================================================================

// $pricing = mm_create_custom_resource('ServicePrice', 'ServicePriceController', 'Pricing')
//     ->setIcon('money-alt')
//     ->setPosition(3);

// $pricing->addPage(
//     mm_create_custom_resource('CurrencyRate', 'CurrencyRateController', 'Currency Rates')
// );
// $pricing->addPage(
//     mm_create_custom_resource('PricingModel', 'PricingModelController', 'Pricing Models')
// );
// $pricing->addPage(
//     mm_create_custom_resource('PricingTier', 'PricingTierController', 'Pricing Tiers')
// );
// $pricing->addPage(
//     mm_create_custom_resource('PriceHistory', 'PriceHistoryController', 'Price History', false)
// );


// // ============================================================================
// // EQUIPMENT MANAGEMENT
// // ============================================================================

// $equipment = mm_create_custom_resource('Equipment', 'EquipmentController', 'Equipment')
//     ->setIcon('admin-tools')
//     ->setPosition(3);

// $equipment->addPage(
//     mm_create_custom_resource('ServiceEquipment', 'ServiceEquipmentController', 'Service Equipment')
// );


// // ============================================================================
// // COVERAGE MANAGEMENT
// // ============================================================================

// $coverage = mm_create_custom_resource('ServiceCoverage', 'ServiceCoverageController', 'Coverage')
//     ->setIcon('location-alt')
//     ->setPosition(3);

// $coverage->addPage(
//     mm_create_custom_resource('CoverageArea', 'CoverageAreaController', 'Coverage Areas')
// );


// // ============================================================================
// // DELIVERY MANAGEMENT
// // ============================================================================

// $delivery = mm_create_custom_resource('ServiceDelivery', 'ServiceDeliveryController', 'Delivery')
//     ->setIcon('migrate')
//     ->setPosition(3);

// $delivery->addPage(
//     mm_create_custom_resource('Deliverable', 'DeliverableController', 'Deliverables')
// );
// $delivery->addPage(
//     mm_create_custom_resource('ServiceDeliverable', 'ServiceDeliverableController', 'Service Deliverables')
// );
// $delivery->addPage(
//     mm_create_custom_resource('DeliveryMethod', 'DeliveryMethodController', 'Delivery Method')

// );


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
    mm_create_custom_resource('PriceHistory', 'PriceHistoryController', 'Pricing History', false),

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
