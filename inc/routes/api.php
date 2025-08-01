<?php

use MakerMaker\Controllers\Api\V1\ServiceController as ApiServiceController;
use TypeRocket\Http\Route;

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

// Core CRUD operations
tr_route()->get()->match('api/v1/services')->do([ApiServiceController::class, 'index']);
tr_route()->get()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'show']);
tr_route()->post()->match('api/v1/services')->do([ApiServiceController::class, 'create']);
tr_route()->put()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'update']);
tr_route()->patch()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'patch']);
tr_route()->delete()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'destroy']);

// Status filtering endpoints
tr_route()->get()->match('api/v1/services/active')->do([ApiServiceController::class, 'active']);
tr_route()->get()->match('api/v1/services/inactive')->do([ApiServiceController::class, 'inactive']);

// Search and lookup endpoints
tr_route()->get()->match('api/v1/services/search/(.+)')->do([ApiServiceController::class, 'search']);
tr_route()->get()->match('api/v1/services/code/([a-z_]+)')->do([ApiServiceController::class, 'showByCode']);

// Status management endpoints
tr_route()->post()->match('api/v1/services/([0-9]+)/activate')->do([ApiServiceController::class, 'activate']);
tr_route()->post()->match('api/v1/services/([0-9]+)/deactivate')->do([ApiServiceController::class, 'deactivate']);

// Bulk operations endpoint
tr_route()->post()->match('api/v1/services/bulk')->do([ApiServiceController::class, 'bulk']);