<?php

use MakerMaker\Controllers\Api\V1\ServiceController as ApiServiceController;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your WordPress site. These
| routes are loaded from the TypeRocket\Http\RouteCollection immediately
| after the typerocket_routes action is fired.
|
*/


tr_route()->get()->match('api/v1/services')->do([ApiServiceController::class, 'indexRest']);
tr_route()->get()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'showRest']);
tr_route()->post()->match('api/v1/services')->do([ApiServiceController::class, 'createRest']);
tr_route()->put()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'updateRest']);
tr_route()->patch()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'patchRest']);
tr_route()->delete()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'destroyRest']);

tr_route()->get()->match('api/v1/services/active')->do([ApiServiceController::class, 'activeServicesRest']);
tr_route()->get()->match('api/v1/services/inactive')->do([ApiServiceController::class, 'inactiveServicesRest']);
tr_route()->get()->match('api/v1/services/search/(.+)')->do([ApiServiceController::class, 'searchRest']);
tr_route()->post()->match('api/v1/services/([0-9]+)/activate')->do([ApiServiceController::class, 'activateRest']);
tr_route()->post()->match('api/v1/services/([0-9]+)/deactivate')->do([ApiServiceController::class, 'deactivateRest']);
tr_route()->get()->match('api/v1/services/code/([a-z_]+)')->do([ApiServiceController::class, 'showByCodeRest']);
tr_route()->post()->match('api/v1/services/bulk')->do([ApiServiceController::class, 'bulkOperationsRest']);