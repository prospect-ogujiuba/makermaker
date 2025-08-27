<?php

// Main Service resource
$service = createServiceResource('Service', 'ServiceController', 'Services')->setIcon('cart')->setPosition(2);

// Create all service subpages
$serviceSubpages = [
    createServiceResource('ServiceComplexity', 'ServiceComplexityController', 'Complexities'),
    createServiceResource('ServicePricingModel', 'ServicePricingModelController', 'Pricing Models'),
    createServiceResource('ServicePricingTier', 'ServicePricingTierController', 'Pricing Tiers'),
    createServiceResource('ServiceDeliveryMethod', 'ServiceDeliveryMethodController', 'Delivery Methods'),
    createServiceResource('ServiceCoverageArea', 'ServiceCoverageAreaController', 'Coverage Areas'),
    createServiceResource('ServiceDeliverable', 'ServiceDeliverableController', 'Deliverables'),
    createServiceResource('ServiceEquipment', 'ServiceEquipmentController', 'Equipment'),
    createServiceResource('ServiceType', 'ServiceTypeController', 'Types'),
    createServiceResource('ServiceCategory', 'ServiceCategoryController', 'Categories'),
    createServiceResource('ServiceAttributeDefinition', 'ServiceAttributeDefinitionController', 'Attribute Definitions'),
];

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}
