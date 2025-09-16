<?php

// Main Service resource
$service = mm_create_custom_resource('Service', 'ServiceController', 'Services')->setIcon('cart')->setPosition(2);

// Create all service subpages
$serviceSubpages = [
    mm_create_custom_resource('ServiceComplexity', 'ServiceComplexityController', 'Complexities'),
    mm_create_custom_resource('ServicePricingModel', 'ServicePricingModelController', 'Pricing Models'),
    mm_create_custom_resource('ServicePricingTier', 'ServicePricingTierController', 'Pricing Tiers'),
    mm_create_custom_resource('ServiceDeliveryMethod', 'ServiceDeliveryMethodController', 'Delivery Methods'),
    mm_create_custom_resource('ServiceCoverageArea', 'ServiceCoverageAreaController', 'Coverage Areas'),
    mm_create_custom_resource('ServiceDeliverable', 'ServiceDeliverableController', 'Deliverables'),
    mm_create_custom_resource('ServiceEquipment', 'ServiceEquipmentController', 'Equipment'),
    mm_create_custom_resource('ServiceType', 'ServiceTypeController', 'Types'),
    mm_create_custom_resource('ServiceCategory', 'ServiceCategoryController', 'Categories'),
    mm_create_custom_resource('ServiceAttributeDefinition', 'ServiceAttributeDefinitionController', 'Attribute Definitions'),
    mm_create_custom_resource('ServiceBundle', 'ServiceBundleController', 'Bundle Offers'),
    mm_create_custom_resource('ServicePrice', 'ServicePriceController', 'Prices'),
    mm_create_custom_resource('ServiceAddon', 'ServiceAddonController', 'Addons'),
    mm_create_custom_resource('ServiceAttributeValue', 'ServiceAttributeValueController', 'Attribute Values'),
    mm_create_custom_resource('ServiceCoverage', 'ServiceCoverageController', 'Coverages'),
    mm_create_custom_resource('ServiceDeliverableAssignment', 'ServiceDeliverableAssignmentController', 'Deliverables'),
    mm_create_custom_resource('ServiceDeliveryMethodAssignment', 'ServiceDeliveryMethodAssignmentController', 'Delivery Methods'),
    mm_create_custom_resource('ServiceEquipmentAssignment', 'ServiceEquipmentAssignmentController', 'Equipment'),
    mm_create_custom_resource('ServiceRelationship', 'ServiceRelationshipController', 'Relationships'),
    mm_create_custom_resource('ServiceBundleItem', 'ServiceBundleItemController', 'Bundle Items'),
];

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}
