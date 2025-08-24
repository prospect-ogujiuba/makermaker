<?php

// Main Quote resource
$quote = tr_resource_pages('Quote@\MakerMaker\Controllers\Web\QuoteController', 'Quotes')
    ->setIcon('media-document')
    ->setPosition(2);

$quoteCategory = tr_resource_pages('QuoteCategory@\MakerMaker\Controllers\Web\QuoteCategoryController', 'Category');

// Add child pages to main quote resource
$quote->addPage($quoteCategory);
