<?php

// Main Service resource
$service = tr_resource_pages('Service@\MakerMaker\Controllers\ServiceController', 'Services')
    ->setIcon('cart')
    ->setPosition(2);

// Create all service subpages
$serviceSubpages = [
    createServiceResource('ServiceComplexity', 'ServiceComplexityController', 'Complexities'),
    createServiceResource('ServicePricingModel', 'ServicePricingModelController', 'Pricing Models'),
];

// Add all subpages to the main service resource
foreach ($serviceSubpages as $subpage) {
    $service->addPage($subpage);
}
