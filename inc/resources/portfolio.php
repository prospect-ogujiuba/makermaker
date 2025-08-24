<?php

// Main Portfolio resource
$portfolio = tr_resource_pages('Portfolio@\MakerMaker\Controllers\Web\PortfolioController', 'Portfolios')
    ->setIcon('portfolio')
    ->setPosition(2);

$portfolioCategory = tr_resource_pages('PortfolioCategory@\MakerMaker\Controllers\Web\PortfolioCategoryController', 'Category');

// Add child pages to main portfolio resource
$portfolio->addPage($portfolioCategory);
