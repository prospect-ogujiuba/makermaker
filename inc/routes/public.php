<?php

use MakerMaker\Controllers\ServiceController;
use MakerMaker\Http\Fields\ServiceFields;
use MakerMaker\Models\Service;
use MakerMaker\View;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your WordPress site. These
| routes are loaded from the TypeRocket\Http\RouteCollection immediately
| after the typerocket_routes action is fired.
|
*/

tr_route()->get()->match('api/v1/services')->do([ServiceController::class, 'indexRest']);
tr_route()->get()->match('api/v1/services/([0-9]+)')->do([ServiceController::class, 'showRest']);
tr_route()->post()->match('api/v1/services')->do([ServiceController::class, 'createRest']);
tr_route()->put()->match('api/v1/services/([0-9]+)')->do([ServiceController::class, 'updateRest']);
tr_route()->delete()->match('api/v1/services/([0-9]+)')->do([ServiceController::class, 'destroyRest']);