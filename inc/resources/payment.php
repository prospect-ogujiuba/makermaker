<?php

// Main Payment resource
$payment = tr_resource_pages('Payment@\MakerMaker\Controllers\Web\PaymentController', 'Payments')
    ->setIcon('yes-alt')
    ->setPosition(2);

$paymentCategory = tr_resource_pages('PaymentCategory@\MakerMaker\Controllers\Web\PaymentCategoryController', 'Category');

// Add child pages to main payment resource
$payment->addPage($paymentCategory);
