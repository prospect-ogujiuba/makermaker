<?php

/*
|--------------------------------------------------------------------------
| Customer Resource
|--------------------------------------------------------------------------
|
| This file is used to register and configure the customer resource
| within the WordPress admin interface.
|
*/

// Create the main resource
$customer = mm_create_custom_resource('Customer', 'CustomerController', 'Customers')
    ->setIcon('dashicons-groups')
    ->setPosition(2);

// Optional: Add sub-pages for customer management
// $customer->addPage(
//     mm_create_custom_resource('CustomerGroup', 'CustomerGroupController', 'Groups')
// );
// 
// $customer->addPage(
//     mm_create_custom_resource('CustomerNote', 'CustomerNoteController', 'Notes')
// );