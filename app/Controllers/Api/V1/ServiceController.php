<?php

namespace MakerMaker\Controllers\Api\V1;

use MakerMaker\Controllers\Web\ServiceController as WebServiceController;
use MakerMaker\Http\Fields\ServiceFields;
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
    public function index(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            // For public API, we may not require auth for reading services
            // But we still check if service reading is allowed
            $service = new $this->modelClass;
            if ($user && !$service->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot access services', 403);
            }

            $services = Service::new()
                ->where('deleted_at', null)
                ->findAll()
                ->get();

            $response->setStatus(200);
            $response->setMessage('Services fetched successfully');
            $response->setData('services', $services,);
            $response->setData('count', count($services));

            return $response;
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to fetch services', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Get single service
     * GET /api/v1/services/{id}
     */
    public function show($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);

            if (!$service || $service->deleted_at) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization if user is provided
            if ($user && !$service->can('read', $user)) {
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
    public function create(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            $model = new $this->modelClass;

            // Check authorization - creation requires authentication
            if (!$user || !$model->can('create', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot create service', 403);
            }

            // Parse input data
            $inputData = $this->parseRequestData($request);

            // Check for duplicate service code
            if (isset($inputData['code'])) {
                $existingService = Service::new()->where('code', $inputData['code'])->first();
                if ($existingService) {
                    return $this->apiError($response, 'Service with this code already exists', 409);
                }
            }

            // Use the working code
            $fields = new ServiceFields($inputData);
            $controller = new WebServiceController();
            $controller->create($fields, new Service(), $response);

            return $response;
        } catch (ModelException $e) {
            $this->onAction('error', 'create', $e, $model ?? null);
            return $this->apiError($response, 'Failed to create service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to create service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Update existing service (full update)
     * PUT /api/v1/services/{id}
     */
    public function update($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service || $service->deleted_at) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$user || !$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            // Parse input data
            $inputData = $this->parseRequestData($request);

            // Check for duplicate service code (excluding current service)
            if (isset($inputData['code']) && $inputData['code'] !== $service->code) {
                $existingService = Service::new()
                    ->where('code', $inputData['code'])
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingService) {
                    return $this->apiError($response, 'Service with this code already exists', 409);
                }
            }

            do_action('makermaker_api_service_update', $this, $service, $user);

            // Use the working validation and update approach
            $fields = new ServiceFields($inputData);
            $controller = new WebServiceController();
            $controller->update($service, $fields, $response);

            do_action('makermaker_api_service_after_update', $this, $service, $user);

            return $response;
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
    public function patch($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service || $service->deleted_at) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$user || !$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            // Parse input data
            $inputData = $this->parseRequestData($request);

            if (empty($inputData)) {
                return $this->apiError($response, 'No data provided for update', 400);
            }

            // Check for duplicate service code (excluding current service)
            if (isset($inputData['code']) && $inputData['code'] !== $service->code) {
                $existingService = Service::new()
                    ->where('code', $inputData['code'])
                    ->where('id', '!=', $id)
                    ->first();
                if ($existingService) {
                    return $this->apiError($response, 'Service with this code already exists', 409);
                }
            }

            do_action('makermaker_api_service_patch', $this, $service, $user);

            // For PATCH, use partial validation and direct update
            $fields = new ServiceFields($inputData);
            $fields->setPartialValidation(true);

            // Validate provided fields
            if (!$fields->isValid()) {
                return $this->apiError($response, 'Validation failed', 422, $fields->getApiErrors());
            }

            // Update only provided and validated fields
            $service->update($fields->getFieldData());
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
    public function destroy($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service || $service->deleted_at) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$user || !$service->can('destroy', $user)) {
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
    public function active(Request $request, Response $response, ?AuthUser $user = null)
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
    public function inactive(Request $request, Response $response, ?AuthUser $user = null)
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
    public function search($query = null, Request $request, Response $response, ?AuthUser $user = null)
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
    public function showByCode($code = null, Request $request, Response $response, ?AuthUser $user = null)
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
    public function activate($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        return $this->toggleStatus($id, 1, 'activated', $request, $response, $user);
    }

    /**
     * REST API: Deactivate service
     * POST /api/v1/services/{id}/deactivate
     */
    public function deactivate($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        return $this->toggleStatus($id, 0, 'deactivated', $request, $response, $user);
    }

    /**
     * REST API: Bulk operations
     * POST /api/v1/services/bulk
     */
    public function bulk(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            // Check authorization for bulk operations
            if (!$user) {
                return $this->apiError($response, 'Authentication required for bulk operations', 401);
            }

            $inputData = $this->parseRequestData($request);

            if (!isset($inputData['action']) || !isset($inputData['service_ids'])) {
                return $this->apiError($response, 'Action and service_ids are required', 400);
            }

            $action = $inputData['action'];
            $serviceIds = $inputData['service_ids'];
            $results = [];
            $errors = [];

            if (!is_array($serviceIds)) {
                return $this->apiError($response, 'service_ids must be an array', 400);
            }

            foreach ($serviceIds as $id) {
                try {
                    $service = Service::new()->findById($id);
                    if (!$service || $service->deleted_at) {
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

                        case 'delete':
                            if (!$service->can('destroy', $user)) {
                                $errors[] = "Unauthorized to delete service with ID {$id}";
                                continue 2;
                            }
                            $service->delete();
                            $results[] = "Service {$id} deleted";
                            break;

                        case 'update_pricing':
                            if (isset($inputData['new_price']) && is_numeric($inputData['new_price'])) {
                                $service->base_price = $inputData['new_price'];
                                $service->save();
                                $results[] = "Service {$id} price updated to {$inputData['new_price']}";
                            } else {
                                $errors[] = "new_price required and must be numeric for update_pricing action";
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
     * Parse request data from various HTTP methods
     */
    private function parseRequestData(Request $request): array
    {
        // Try to get JSON data first
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        // If not JSON, try form data
        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            parse_str($input, $data);
        }

        // Fallback to request fields
        if (empty($data)) {
            $data = $request->getFields();
        }

        return $data ?: [];
    }

    /**
     * Validate PATCH request data
     */
    private function validatePatchData(array $data): bool
    {
        $allowedFields = ['code', 'name', 'description', 'base_price', 'icon', 'active'];

        foreach ($data as $field => $value) {
            if (!in_array($field, $allowedFields)) {
                continue; // Skip unknown fields
            }

            // Validate specific fields
            if ($field === 'base_price' && (!is_numeric($value) || $value < 0)) {
                return false;
            }

            if ($field === 'active' && !in_array($value, ['0', '1', 0, 1, true, false])) {
                return false;
            }

            if ($field === 'code' && !preg_match('/^[a-z_]+$/', $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Toggle service status (activate/deactivate)
     */
    private function toggleStatus($id, int $status, string $action, Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service || $service->deleted_at) {
                return $this->apiError($response, 'Service not found', 404);
            }

            if (!$user || !$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            $service->active = $status;
            $service->save();

            return $this->apiSuccess($response, [
                'message' => "Service {$action} successfully",
                'data' => $service
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, "Failed to {$action} service", 500, $e->getMessage());
        }
    }

    /**
     * Helper method for API success responses
     */
    private function apiSuccess(Response $response, array $data, int $status = 200): Response
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
    private function apiError(Response $response, string $message, int $status = 400, $details = null): Response
    {
        $response->setStatus($status);
        $response->setData('success', false);
        $response->setData('error', $message);

        if ($details) {
            if (is_array($details)) {
                $response->setData('validation_errors', $details);
            } else {
                $response->setData('details', $details);
            }
        }

        return $response;
    }
}
