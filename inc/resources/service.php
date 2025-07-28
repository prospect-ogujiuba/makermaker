<?php

use MakerMaker\Controllers\ServiceController;

$service = tr_resource_pages('Service@\MakerMaker\Controllers\ServiceController', 'Service')
    ->setIcon('cart')
    ->setPosition(2);

\TypeRocket\Register\Registry::addCustomResource('service', [
    'controller' => ServiceController::class,
]);
