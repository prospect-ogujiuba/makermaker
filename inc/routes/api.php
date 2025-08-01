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

// ================================
// CORE CRUD OPERATIONS
// ================================

// Standard REST API endpoints
tr_route()->get()->match('api/v1/services')->do([ApiServiceController::class, 'index']);
tr_route()->get()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'show']);
tr_route()->post()->match('api/v1/services')->do([ApiServiceController::class, 'create']);
tr_route()->put()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'update']);
tr_route()->patch()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'patch']);
tr_route()->delete()->match('api/v1/services/([0-9]+)')->do([ApiServiceController::class, 'destroy']);
tr_route()->post()->match('api/v1/services/generate-code')->do([ApiServiceController::class, 'generateCode']);

// ================================
// SOFT DELETE MANAGEMENT
// ================================

// Get only deleted services
tr_route()->get()->match('api/v1/services/deleted')->do([ApiServiceController::class, 'deleted']);

// Restore a soft deleted service
tr_route()->post()->match('api/v1/services/([0-9]+)/restore')->do([ApiServiceController::class, 'restore']);

// Permanently delete a service (cannot be undone)
tr_route()->delete()->match('api/v1/services/([0-9]+)/force')->do([ApiServiceController::class, 'forceDelete']);

// ================================
// STATUS FILTERING ENDPOINTS  
// ================================

// Get only active services (not deleted)
tr_route()->get()->match('api/v1/services/active')->do([ApiServiceController::class, 'active']);

// Get only inactive services (not deleted)
tr_route()->get()->match('api/v1/services/inactive')->do([ApiServiceController::class, 'inactive']);

// ================================
// SEARCH AND LOOKUP ENDPOINTS
// ================================

// Search services by name, code, or description
tr_route()->get()->match('api/v1/services/search/(.+)')->do([ApiServiceController::class, 'search']);

// Get service by code instead of ID
tr_route()->get()->match('api/v1/services/code/([a-z_]+)')->do([ApiServiceController::class, 'showByCode']);

// ================================
// STATUS MANAGEMENT ENDPOINTS
// ================================

// Activate a service (set active = 1)
tr_route()->post()->match('api/v1/services/([0-9]+)/activate')->do([ApiServiceController::class, 'activate']);

// Deactivate a service (set active = 0)  
tr_route()->post()->match('api/v1/services/([0-9]+)/deactivate')->do([ApiServiceController::class, 'deactivate']);

// ================================
// BULK OPERATIONS ENDPOINT
// ================================

// Bulk operations: activate, deactivate, or soft delete multiple services
tr_route()->post()->match('api/v1/services/bulk')->do([ApiServiceController::class, 'bulk']);

/*
|--------------------------------------------------------------------------
| Route Usage Examples
|--------------------------------------------------------------------------
|
| BASIC CRUD:
| GET    /api/v1/services                    - List all services (excludes deleted)
| GET    /api/v1/services?include_deleted=true - List all services including deleted  
| GET    /api/v1/services/123               - Get single service
| POST   /api/v1/services                   - Create new service
| PUT    /api/v1/services/123               - Full update of service
| PATCH  /api/v1/services/123               - Partial update of service
| DELETE /api/v1/services/123               - Soft delete service
|
| SOFT DELETE MANAGEMENT:
| GET    /api/v1/services/deleted           - Get only soft deleted services
| POST   /api/v1/services/123/restore       - Restore soft deleted service
| DELETE /api/v1/services/123/force         - Permanently delete (cannot undo)
|
| STATUS FILTERING:
| GET    /api/v1/services/active            - Get only active services
| GET    /api/v1/services/inactive          - Get only inactive services
|
| SEARCH & LOOKUP: 
| GET    /api/v1/services/search/hosting    - Search for "hosting" in name/code/description
| GET    /api/v1/services/code/voip_premium - Get service with code "voip_premium"
|
| STATUS MANAGEMENT:
| POST   /api/v1/services/123/activate      - Set service as active
| POST   /api/v1/services/123/deactivate    - Set service as inactive
|
| BULK OPERATIONS:
| POST   /api/v1/services/bulk              - Body: {"action": "activate", "ids": [1,2,3]}
|                                           - Body: {"action": "deactivate", "ids": [1,2,3]}  
|                                           - Body: {"action": "delete", "ids": [1,2,3]}
|
*/