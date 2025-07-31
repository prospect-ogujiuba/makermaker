<?php

namespace MakerMaker\Controllers\Api\V1;

use MakerMaker\Models\Service;
use TypeRocket\Controllers\Controller;

class ServiceController extends Controller
{
    public function indexRest()
    {
        try {
            // Check authorization (optional - you may want to allow public access to service listings)
            $service = Service::new();
            if (!$service->can('read')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot access services'
                ];
            }

            $services = Service::new()
                ->where('deleted_at', null) // Only non-deleted services
                ->findAll()
                ->get();

            return [
                'success' => true,
                'data' => $services,
                'count' => count($services)
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to fetch services',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Get single service
     * GET /api/v1/services/{id}
     */
    public function showRest($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service ID is required'
                ];
            }

            $service = Service::new()->findById($id);

            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            // Check if service is soft deleted
            if ($service->deleted_at) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            // Check authorization
            if (!$service->can('read')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot access this service'
                ];
            }

            return [
                'success' => true,
                'data' => $service
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to fetch service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Create new service
     * POST /api/v1/services
     */
    public function createRest()
    {
        try {
            // Check authorization
            $service = Service::new();
            if (!$service->can('create')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot create service'
                ];
            }

            // Validate required fields
            $requiredFields = ['code', 'name', 'description', 'base_price', 'icon', 'active'];
            $data = [];

            foreach ($requiredFields as $field) {
                if (!isset($_POST[$field]) || $_POST[$field] === '') {
                    http_response_code(400);
                    return [
                        'success' => false,
                        'error' => "Field '{$field}' is required"
                    ];
                }
                $data[$field] = $_POST[$field];
            }

            // Additional validation
            if (!is_numeric($data['base_price']) || $data['base_price'] < 0) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Base price must be a valid number >= 0'
                ];
            }

            if (!in_array($data['active'], ['0', '1', 0, 1])) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Active status must be 0 or 1'
                ];
            }

            // Check if code already exists
            $existingService = Service::new()->where('code', $data['code'])->first();
            if ($existingService) {
                http_response_code(409);
                return [
                    'success' => false,
                    'error' => 'Service with this code already exists'
                ];
            }

            // Create service
            $newService = Service::new();
            $newService->fill($data);
            $newService->save();

            http_response_code(201);
            return [
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $newService
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to create service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Update existing service (full update)
     * PUT /api/v1/services/{id}
     */
    public function updateRest($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service ID is required'
                ];
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            // Check authorization
            if (!$service->can('update')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot update service'
                ];
            }

            // Get PUT data (PHP doesn't populate $_POST for PUT requests)
            $putData = json_decode(file_get_contents('php://input'), true);
            if (!$putData) {
                parse_str(file_get_contents('php://input'), $putData);
            }

            // Validate required fields for full update
            $requiredFields = ['code', 'name', 'description', 'base_price', 'icon', 'active'];
            $data = [];

            foreach ($requiredFields as $field) {
                if (!isset($putData[$field]) || $putData[$field] === '') {
                    http_response_code(400);
                    return [
                        'success' => false,
                        'error' => "Field '{$field}' is required for full update"
                    ];
                }
                $data[$field] = $putData[$field];
            }

            // Additional validation
            if (!is_numeric($data['base_price']) || $data['base_price'] < 0) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Base price must be a valid number >= 0'
                ];
            }

            if (!in_array($data['active'], ['0', '1', 0, 1])) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Active status must be 0 or 1'
                ];
            }

            // Check if code conflicts with another service
            if ($data['code'] !== $service->code) {
                $existingService = Service::new()->where('code', $data['code'])->where('id', '!=', $id)->first();
                if ($existingService) {
                    http_response_code(409);
                    return [
                        'success' => false,
                        'error' => 'Service with this code already exists'
                    ];
                }
            }

            // Update service
            $service->fill($data);
            $service->save();

            return [
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to update service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Partial update existing service
     * PATCH /api/v1/services/{id}
     */
    public function patchRest($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service ID is required'
                ];
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            // Check authorization
            if (!$service->can('update')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot update service'
                ];
            }

            // Get PATCH data
            $patchData = json_decode(file_get_contents('php://input'), true);
            if (!$patchData) {
                parse_str(file_get_contents('php://input'), $patchData);
            }

            if (empty($patchData)) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'No data provided for update'
                ];
            }

            // Validate provided fields
            $allowedFields = ['code', 'name', 'description', 'base_price', 'icon', 'active'];
            $data = [];

            foreach ($patchData as $field => $value) {
                if (!in_array($field, $allowedFields)) {
                    continue; // Skip unknown fields
                }

                // Validate specific fields
                if ($field === 'base_price' && (!is_numeric($value) || $value < 0)) {
                    http_response_code(400);
                    return [
                        'success' => false,
                        'error' => 'Base price must be a valid number >= 0'
                    ];
                }

                if ($field === 'active' && !in_array($value, ['0', '1', 0, 1])) {
                    http_response_code(400);
                    return [
                        'success' => false,
                        'error' => 'Active status must be 0 or 1'
                    ];
                }

                $data[$field] = $value;
            }

            // Check if code conflicts with another service
            if (isset($data['code']) && $data['code'] !== $service->code) {
                $existingService = Service::new()->where('code', $data['code'])->where('id', '!=', $id)->first();
                if ($existingService) {
                    http_response_code(409);
                    return [
                        'success' => false,
                        'error' => 'Service with this code already exists'
                    ];
                }
            }

            // Update only provided fields
            $service->fill($data);
            $service->save();

            return [
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to update service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Delete service (soft delete)
     * DELETE /api/v1/services/{id}
     */
    public function destroyRest($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service ID is required'
                ];
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            // Check authorization
            if (!$service->can('destroy')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot delete service'
                ];
            }

            // Perform soft delete
            $service->delete();

            return [
                'success' => true,
                'message' => 'Service deleted successfully'
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to delete service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Get only active services
     * GET /api/v1/services/active
     */
    public function activeServicesRest()
    {
        try {
            $services = Service::new()
                ->where('active', 1)
                ->where('deleted_at', null)
                ->findAll()
                ->get();

            return [
                'success' => true,
                'data' => $services,
                'count' => count($services)
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to fetch active services',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Get only inactive services
     * GET /api/v1/services/inactive
     */
    public function inactiveServicesRest()
    {
        try {
            $services = Service::new()
                ->where('active', 0)
                ->where('deleted_at', null)
                ->findAll()
                ->get();

            return [
                'success' => true,
                'data' => $services,
                'count' => count($services)
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to fetch inactive services',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Search services
     * GET /api/v1/services/search/{query}
     */
    public function searchRest($query = null)
    {
        try {
            if (!$query) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Search query is required'
                ];
            }

            $query = urldecode($query);
            $services = Service::new()
                ->where('deleted_at', null)
                ->where(function ($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query . '%')
                        ->orWhere('code', 'LIKE', '%' . $query . '%')
                        ->orWhere('description', 'LIKE', '%' . $query . '%');
                })
                ->findAll()
                ->get();

            return [
                'success' => true,
                'data' => $services,
                'count' => count($services),
                'query' => $query
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to search services',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Get service by code
     * GET /api/v1/services/code/{code}
     */
    public function showByCodeRest($code = null)
    {
        try {
            if (!$code) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service code is required'
                ];
            }

            $service = Service::new()
                ->where('code', $code)
                ->where('deleted_at', null)
                ->first();

            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            return [
                'success' => true,
                'data' => $service
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to fetch service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Activate service
     * POST /api/v1/services/{id}/activate
     */
    public function activateRest($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service ID is required'
                ];
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            if (!$service->can('update')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot update service'
                ];
            }

            $service->active = 1;
            $service->save();

            return [
                'success' => true,
                'message' => 'Service activated successfully',
                'data' => $service
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to activate service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Deactivate service
     * POST /api/v1/services/{id}/deactivate
     */
    public function deactivateRest($id = null)
    {
        try {
            if (!$id) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Service ID is required'
                ];
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                http_response_code(404);
                return [
                    'success' => false,
                    'error' => 'Service not found'
                ];
            }

            if (!$service->can('update')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized: Cannot update service'
                ];
            }

            $service->active = 0;
            $service->save();

            return [
                'success' => true,
                'message' => 'Service deactivated successfully',
                'data' => $service
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to deactivate service',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * REST API: Bulk operations
     * POST /api/v1/services/bulk
     */
    public function bulkOperationsRest()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                parse_str(file_get_contents('php://input'), $data);
            }

            if (!isset($data['action']) || !isset($data['service_ids'])) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'Action and service_ids are required'
                ];
            }

            $action = $data['action'];
            $serviceIds = $data['service_ids'];
            $results = [];
            $errors = [];

            if (!is_array($serviceIds)) {
                http_response_code(400);
                return [
                    'success' => false,
                    'error' => 'service_ids must be an array'
                ];
            }

            foreach ($serviceIds as $id) {
                try {
                    $service = Service::new()->findById($id);
                    if (!$service) {
                        $errors[] = "Service with ID {$id} not found";
                        continue;
                    }

                    if (!$service->can('update')) {
                        $errors[] = "Unauthorized to update service with ID {$id}";
                        continue;
                    }

                    switch ($action) {
                        case 'activate':
                            $service->active = 1;
                            $service->save();
                            $results[] = "Service {$id} activated";
                            break;

                        case 'deactivate':
                            $service->active = 0;
                            $service->save();
                            $results[] = "Service {$id} deactivated";
                            break;

                        case 'update_pricing':
                            if (isset($data['new_price'])) {
                                $service->base_price = $data['new_price'];
                                $service->save();
                                $results[] = "Service {$id} price updated to {$data['new_price']}";
                            } else {
                                $errors[] = "new_price required for update_pricing action";
                            }
                            break;

                        default:
                            $errors[] = "Unknown action: {$action}";
                            break;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing service {$id}: " . $e->getMessage();
                }
            }

            return [
                'success' => count($errors) === 0,
                'results' => $results,
                'errors' => $errors,
                'processed' => count($results),
                'failed' => count($errors)
            ];
        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Failed to perform bulk operation',
                'message' => $e->getMessage()
            ];
        }
    }
}
