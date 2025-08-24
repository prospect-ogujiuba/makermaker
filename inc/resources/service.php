<?php

// Main Service resource
$service = tr_resource_pages('Service@\MakerMaker\Controllers\Web\ServiceController', 'Services')
    ->setIcon('cart')
    ->setPosition(2);

$serviceAddons = tr_resource_pages('ServiceAddons@\MakerMaker\Controllers\Web\ServiceAddonController', 'Addons');
$serviceBundles = tr_resource_pages('ServiceBundles@\MakerMaker\Controllers\Web\ServiceBundleController', 'Bundles');
$serviceCategory = tr_resource_pages('ServiceCategory@\MakerMaker\Controllers\Web\ServiceCategoryController', 'Categories');
$serviceComplexity = tr_resource_pages('ServiceComplexity@\MakerMaker\Controllers\Web\ServiceComplexityController', 'Service Complexity');
$serviceCoverageArea = tr_resource_pages('ServiceCoverageArea@\MakerMaker\Controllers\Web\ServiceCoverageAreaController', 'Coverage Areas');
$serviceDeliverables = tr_resource_pages('ServiceDeliverables@\MakerMaker\Controllers\Web\ServiceDeliverableController', 'Deliverables');
$serviceDeliveryMethod = tr_resource_pages('ServiceDeliveryMethod@\MakerMaker\Controllers\Web\ServiceDeliveryMethodController', 'Delivery Method');
$serviceDependency = tr_resource_pages('ServiceDependency@\MakerMaker\Controllers\Web\ServiceDependencyController', 'Dependencies');
$serviceEquipment = tr_resource_pages('ServiceEquipment@\MakerMaker\Controllers\Web\ServiceEquipmentController', 'Equipment');
$servicePrerequisite = tr_resource_pages('ServicePrerequisite@\MakerMaker\Controllers\Web\ServicePrerequisiteController', 'Prerequisites');
$servicePricingTier = tr_resource_pages('ServicePricingTier@\MakerMaker\Controllers\Web\ServicePricingTierController', 'Pricing Tiers');
$servicePricingModel = tr_resource_pages('ServicePricingModel@\MakerMaker\Controllers\Web\ServicePricingModelController', 'Pricing Models');
$serviceType = tr_resource_pages('ServiceType@\MakerMaker\Controllers\Web\ServiceTypeController', 'Service Types');

// Add child pages to main service resource
$service->addPage($serviceAddons);
$service->addPage($serviceBundles);
$service->addPage($serviceCategory);
$service->addPage($serviceComplexity);
$service->addPage($serviceCoverageArea);
$service->addPage($serviceDeliverables);
$service->addPage($serviceDeliveryMethod);
$service->addPage($serviceDependency);
$service->addPage($serviceEquipment);
$service->addPage($servicePrerequisite);
$service->addPage($servicePricingTier);
$service->addPage($servicePricingModel);
$service->addPage($serviceType);
