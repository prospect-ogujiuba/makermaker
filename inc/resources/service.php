<?php

use MakerMaker\Controllers\Web\ServiceController;

$service = tr_resource_pages('Service@\MakerMaker\Controllers\Web\ServiceController', 'Services')
    ->setIcon('cart')
    ->setPosition(2);

\TypeRocket\Register\Registry::addCustomResource('services', [
    'controller' => ServiceController::class,
]);
