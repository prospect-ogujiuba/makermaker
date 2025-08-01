<?php

namespace MakerMaker\Controllers\Api\V1;

use MakerMaker\Models\Service;
use TypeRocket\Controllers\Controller;
use TypeRocket\Controllers\Traits\LoadsModel;
use TypeRocket\Exceptions\ModelException;
use TypeRocket\Http\Request;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceController extends Controller
{
    use LoadsModel;

    protected $modelClass = Service::class;

    /**
     * REST API: Get all services
     * GET /api/v1/services
     */
    public function indexRest(Request $request, Response $response, AuthUser $user)
    {
        try {
            // Check authorization (optional - you may want to allow public access to service listings)
            $service = new $this->modelClass;
            if (!$service->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot access services', 403);
            }

            $services = Service::new()
                ->where('deleted_at', null) // Only non-deleted services
                ->findAll()
                ->get();

            return $this->apiSuccess($response, [
                'data' => $services,
                'count' => count($services)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to fetch services', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Get single service
     * GET /api/v1/services/{id}
     */
    public function showRest($id = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);

            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check if service is soft deleted
            if ($service->deleted_at) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$service->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot access this service', 403);
            }

            return $this->apiSuccess($response, ['data' => $service]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to fetch service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Create new service
     * POST /api/v1/services
     */
    public function create(Request $request, Response $response, AuthUser $user)
    {
        /** @var Service $model */
        $model = new $this->modelClass;

        try {
            // Check authorization
            if (!$model->can('create', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot create service', 403);
            }

            // Create service
            $newService = $model->save($request->fields());

            if ($newService) {
                $this->onAction('save', 'create', $newService);
            }

            do_action('makermaker_api_service_after_create', $this, $newService, $user);

            return $this->apiSuccess($response, [
                'message' => 'Service created successfully',
                'data' => $newService
            ], 201);
        } catch (ModelException $e) {
            $this->onAction('error', 'create', $e, $model);
            return $this->apiError($response, 'Failed to create service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to create service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Update existing service (full update)
     * PUT /api/v1/services/{id}
     */
    public function update($id = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
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
                    return $this->apiError($response, "Field '{$field}' is required for full update", 400);
                }
                $data[$field] = $putData[$field];
            }

            // Additional validation
            if (!is_numeric($data['base_price']) || $data['base_price'] < 0) {
                return $this->apiError($response, 'Base price must be a valid number >= 0', 400);
            }

            if (!in_array($data['active'], ['0', '1', 0, 1])) {
                return $this->apiError($response, 'Active status must be 0 or 1', 400);
            }

            // Check if code conflicts with another service
            if ($data['code'] !== $service->code) {
                $existingService = Service::new()->where('code', $data['code'])->where('id', '!=', $id)->first();
                if ($existingService) {
                    return $this->apiError($response, 'Service with this code already exists', 409);
                }
            }

            do_action('makermaker_api_service_update', $this, $service, $user);

            // Update service
            $service->update($data);
            $this->onAction('save', 'update', $service);

            do_action('makermaker_api_service_after_update', $this, $service, $user);

            return $this->apiSuccess($response, [
                'message' => 'Service updated successfully',
                'data' => $service
            ]);
        } catch (ModelException $e) {
            $this->onAction('error', 'update', $e, $service ?? null);
            return $this->apiError($response, 'Failed to update service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to update service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Partial update existing service
     * PATCH /api/v1/services/{id}
     */
    public function patchRest($id = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            // Get PATCH data
            $patchData = json_decode(file_get_contents('php://input'), true);
            if (!$patchData) {
                parse_str(file_get_contents('php://input'), $patchData);
            }

            if (empty($patchData)) {
                return $this->apiError($response, 'No data provided for update', 400);
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
                    return $this->apiError($response, 'Base price must be a valid number >= 0', 400);
                }

                if ($field === 'active' && !in_array($value, ['0', '1', 0, 1])) {
                    return $this->apiError($response, 'Active status must be 0 or 1', 400);
                }

                $data[$field] = $value;
            }

            // Check if code conflicts with another service
            if (isset($data['code']) && $data['code'] !== $service->code) {
                $existingService = Service::new()->where('code', $data['code'])->where('id', '!=', $id)->first();
                if ($existingService) {
                    return $this->apiError($response, 'Service with this code already exists', 409);
                }
            }

            do_action('makermaker_api_service_patch', $this, $service, $user);

            // Update only provided fields
            $service->update($data);
            $this->onAction('save', 'update', $service);

            do_action('makermaker_api_service_after_patch', $this, $service, $user);

            return $this->apiSuccess($response, [
                'message' => 'Service updated successfully',
                'data' => $service
            ]);
        } catch (ModelException $e) {
            $this->onAction('error', 'update', $e, $service ?? null);
            return $this->apiError($response, 'Failed to update service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to update service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Delete service (soft delete)
     * DELETE /api/v1/services/{id}
     */
    public function destroyRest($id = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$service->can('destroy', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot delete service', 403);
            }

            do_action('makermaker_api_service_destroy', $this, $service, $user);

            // Perform soft delete
            $service->delete();
            $this->onAction('destroy', $service);

            do_action('makermaker_api_service_after_destroy', $this, $service, $user);

            return $this->apiSuccess($response, [
                'message' => 'Service deleted successfully'
            ]);
        } catch (ModelException $e) {
            $this->onAction('error', 'destroy', $e, $service ?? null);
            return $this->apiError($response, 'Failed to delete service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to delete service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Get only active services
     * GET /api/v1/services/active
     */
    public function activeServicesRest(Request $request, Response $response, AuthUser $user)
    {
        try {
            $services = Service::new()
                ->where('active', 1)
                ->where('deleted_at', null)
                ->findAll()
                ->get();

            return $this->apiSuccess($response, [
                'data' => $services,
                'count' => count($services)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to fetch active services', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Get only inactive services
     * GET /api/v1/services/inactive
     */
    public function inactiveServicesRest(Request $request, Response $response, AuthUser $user)
    {
        try {
            $services = Service::new()
                ->where('active', 0)
                ->where('deleted_at', null)
                ->findAll()
                ->get();

            return $this->apiSuccess($response, [
                'data' => $services,
                'count' => count($services)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to fetch inactive services', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Search services
     * GET /api/v1/services/search/{query}
     */
    public function searchRest($query = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$query) {
                return $this->apiError($response, 'Search query is required', 400);
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

            return $this->apiSuccess($response, [
                'data' => $services,
                'count' => count($services),
                'query' => $query
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to search services', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Get service by code
     * GET /api/v1/services/code/{code}
     */
    public function showByCodeRest($code = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$code) {
                return $this->apiError($response, 'Service code is required', 400);
            }

            $service = Service::new()
                ->where('code', $code)
                ->where('deleted_at', null)
                ->first();

            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            return $this->apiSuccess($response, ['data' => $service]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to fetch service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Activate service
     * POST /api/v1/services/{id}/activate
     */
    public function activateRest($id = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            if (!$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            $service->active = 1;
            $service->save();

            return $this->apiSuccess($response, [
                'message' => 'Service activated successfully',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to activate service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Deactivate service
     * POST /api/v1/services/{id}/deactivate
     */
    public function deactivateRest($id = null, Request $request, Response $response, AuthUser $user)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            if (!$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            $service->active = 0;
            $service->save();

            return $this->apiSuccess($response, [
                'message' => 'Service deactivated successfully',
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to deactivate service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Bulk operations
     * POST /api/v1/services/bulk
     */
    public function bulkOperationsRest(Request $request, Response $response, AuthUser $user)
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                parse_str(file_get_contents('php://input'), $data);
            }

            if (!isset($data['action']) || !isset($data['service_ids'])) {
                return $this->apiError($response, 'Action and service_ids are required', 400);
            }

            $action = $data['action'];
            $serviceIds = $data['service_ids'];
            $results = [];
            $errors = [];

            if (!is_array($serviceIds)) {
                return $this->apiError($response, 'service_ids must be an array', 400);
            }

            foreach ($serviceIds as $id) {
                try {
                    $service = Service::new()->findById($id);
                    if (!$service) {
                        $errors[] = "Service with ID {$id} not found";
                        continue;
                    }

                    if (!$service->can('update', $user)) {
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

            return $this->apiSuccess($response, [
                'results' => $results,
                'errors' => $errors,
                'processed' => count($results),
                'failed' => count($errors)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to perform bulk operation', 500, $e->getMessage());
        }
    }

    /**
     * Helper method for API success responses
     */
    private function apiSuccess(Response $response, array $data, int $status = 200)
    {
        $response->setStatus($status);
        $response->setData('success', true);

        foreach ($data as $key => $value) {
            $response->setData($key, $value);
        }

        return $response;
    }

    /**
     * Helper method for API error responses
     */
    private function apiError(Response $response, string $message, int $status = 400, string $details = null)
    {
        $response->setStatus($status);
        $response->setData('success', false);
        $response->setData('error', $message);

        if ($details) {
            $response->setData('message', $details);
        }

        return $response;
    }
}
