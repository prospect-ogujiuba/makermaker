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
    createServiceResource('ServiceBundle', 'ServiceBundleController', 'Bundle Offers'),
    createServiceResource('ServicePrice', 'ServicePriceController', 'Service Prices'),
    createServiceResource('ServiceAddon', 'ServiceAddonController', 'Service Addons'),
    createServiceResource('ServiceAttributeValue', 'ServiceAttributeValueController', 'Attribute Values'),
    createServiceResource('ServiceCoverage', 'ServiceCoverageController', 'Service Coverages'),
    createServiceResource('ServiceDeliverableAssignment', 'ServiceDeliverableAssignmentController', 'Service Deliverables'),
    createServiceResource('ServiceDeliveryMethodAssignment', 'ServiceDeliveryMethodAssignmentController', 'Service Delivery Methods'),
    createServiceResource('ServiceEquipmentAssignment', 'ServiceEquipmentAssignmentController', 'Service Equipment'),
    createServiceResource('ServiceRelationship', 'ServiceRelationshipController', 'Service Relationships'),
    createServiceResource('ServiceBundleItem', 'ServiceBundleItemController', 'Service Bundle Items'),
];

$service_complexity_rest = \TypeRocket\Register\Registry::addCustomResource('service-complexity', [
	'controller' => '\MakerMaker\Controllers\ServiceComplexityController',
]);

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}
