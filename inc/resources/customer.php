<?php

// Main Customer resource
$customer = tr_resource_pages('Customer@\MakerMaker\Controllers\Web\CustomerController', 'Customers')
    ->setIcon('groups')
    ->setPosition(2);

$contact = tr_resource_pages('Contact@\MakerMaker\Controllers\Web\ContactController', 'Contacts');
$industry = tr_resource_pages('Industry@\MakerMaker\Controllers\Web\IndustryAddonController', 'Industries');

// Add child pages to main customer resource
$customer->addPage($contact);
$customer->addPage($industry);
