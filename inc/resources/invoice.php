<?php

// Main Invoice resource
$invoice = tr_resource_pages('Invoice@\MakerMaker\Controllers\Web\InvoiceController', 'Invoices')
    ->setIcon('media-spreadsheet')
    ->setPosition(2);

$invoiceCategory = tr_resource_pages('InvoiceCategory@\MakerMaker\Controllers\Web\InvoiceCategoryController', 'Category');

// Add child pages to main invoice resource
$invoice->addPage($invoiceCategory);
