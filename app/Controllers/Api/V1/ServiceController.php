<?php

namespace MakerMaker\Controllers\Api\V1;

use MakerMaker\Controllers\Web\ServiceController as WebServiceController;
use MakerMaker\Http\Fields\ServiceFields;
use MakerMaker\Models\Service;
use TypeRocket\Controllers\Controller;
use TypeRocket\Exceptions\ModelException;
use TypeRocket\Http\Request;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceController extends Controller
{
    protected $modelClass = Service::class;

    // ================================
    // CORE CRUD OPERATIONS
    // ================================

    /**
     * REST API: Get all services (excludes soft deleted by default)
     * GET /api/v1/services
     */
    public function index(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            // Check authorization for reading services
            $model = new $this->modelClass;
            if (!$user || !$model->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot read services', 403);
            }

            // Get query parameters
            $page = (int) ($request->input('page') ?? 1);
            $limit = min((int) ($request->input('limit') ?? 50), 100); // Max 100 per page


            // Build query
            $query = Service::new();
            // Default behavior excludes deleted (handled by Service model scopes)

            // Apply filters
            if ($search = $request->input('search')) {
                $query = $query->where(function ($q) use ($search) {
                    return $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            }

            if ($active = $request->input('active')) {
                $query = $query->where('active', $active === 'true' ? 1 : 0);
            }

            // Get paginated results
            $pager = $query->paginate($limit, $page);
            $services = $pager->getResults();

            // Set response data
            $response->setMessage('Services retrieved successfully');

            return $this->apiSuccess($response, [
                'services' => $services->toArray(),
                'pagination' => [
                    'current_page' => $pager->getCurrentPage(),
                    'per_page' => $pager->getNumberPerPage(),
                    'total' => $pager->getCount(),
                    'total_pages' => $pager->getNumberOfPages(),
                    'has_more' => $pager->getCurrentPage() < $pager->getNumberOfPages()
                ]
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to retrieve services', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Get single service by ID
     * GET /api/v1/services/{id}
     */
    public function show($id = null, Request $request, Response $response, ?AuthUser $user = null)
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
            if (!$user || !$service->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot read service', 403);
            }

            return $this->apiSuccess($response, [
                'service' => $service->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to retrieve service', 500, $e->getMessage());
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

            // Auto-generate code if not provided but name is available
            if (empty($inputData['code']) && !empty($inputData['name'])) {
                $inputData['code'] = Service::generateCodeFromName($inputData['name']);
            }

            // Check for duplicate service code (if code was provided or generated)
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

            $response->setData('service', $fields);

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
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$user || !$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            // Parse input data
            $inputData = $this->parseRequestData($request);

            // Auto-generate code if not provided but name is being updated
            if (empty($inputData['code']) && !empty($inputData['name'])) {
                $inputData['code'] = Service::generateCodeFromName($inputData['name'], $id);
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

            // Use ServiceFields for validation and update
            $fields = new ServiceFields($inputData);
            $controller = new WebServiceController();
            $controller->update($service, $fields, $response);

            $response->setData('service', $service->toArray());

            return $response;
        } catch (ModelException $e) {
            $this->onAction('error', 'update', $e, $service ?? null);
            return $this->apiError($response, 'Failed to update service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to update service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Partial update of service
     * PATCH /api/v1/services/{id}
     */
    public function patch($id = null, Request $request, Response $response, ?AuthUser $user = null)
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
            if (!$user || !$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            // Parse and validate PATCH data
            $inputData = $this->parseRequestData($request);

            if (!$this->validatePatchData($inputData)) {
                return $this->apiError($response, 'Invalid data provided for patch update', 400);
            }

            // Check for duplicate code if being updated
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

            // Define allowed fields for PATCH
            // Get fillable fields from the model
            $allowedFields = $service->getFillableFields();

            // Update only provided fields
            foreach ($inputData as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $service->$field = $value;
                }
            }

            $service->save();
            $this->onAction('patch', $service);

            do_action('makermaker_api_service_after_patch', $this, $service, $user);

            $response->setMessage('Service updated successfully');
            return $this->apiSuccess($response, [
                'service' => $service->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to patch service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Soft delete service
     * DELETE /api/v1/services/{id}
     */
    public function destroy($id = null, Request $request, Response $response, ?AuthUser $user = null)
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
            if (!$user || !$service->can('destroy', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot delete service', 403);
            }

            // Perform soft delete
            $service->softDelete();
            $this->onAction('destroy', $service);

            return $this->apiSuccess($response, [
                'message' => 'Service deleted successfully (soft delete)'
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to delete service', 500, $e->getMessage());
        }
    }


    // ================================
    // SEARCH AND LOOKUP ENDPOINTS
    // ================================

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

            $model = new $this->modelClass;
            if (!$user || !$model->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot search services', 403);
            }

            $services = Service::new()
                ->where('name', 'LIKE', "%{$query}%")
                ->orWhere('code', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();

            return $this->apiSuccess($response, [
                'data' => $services->toArray(),
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

            $service = Service::findByCode($code);
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            // Check authorization
            if (!$user || !$service->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot read service', 403);
            }

            return $this->apiSuccess($response, [
                'data' => $service->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to retrieve service by code', 500, $e->getMessage());
        }
    }

    // ================================
    // STATUS MANAGEMENT ENDPOINTS
    // ================================

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
            $action = $request->input('action');
            $ids = $request->input('ids', []);

            if (!$action || !is_array($ids) || empty($ids)) {
                return $this->apiError($response, 'Action and IDs are required', 400);
            }

            $results = [];
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $service = Service::new()->findById($id);
                    if (!$service) {
                        $errors[] = "Service {$id} not found";
                        continue;
                    }

                    if (!$user || !$service->can('update', $user)) {
                        $errors[] = "Unauthorized to modify service {$id}";
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
                            $service->softDelete();
                            $results[] = "Service {$id} deleted";
                            break;
                        default:
                            $errors[] = "Unknown action: {$action}";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing service {$id}: " . $e->getMessage();
                }
            }

            return $this->apiSuccess($response, [
                'message' => 'Bulk operation completed',
                'results' => $results,
                'errors' => $errors,
                'processed' => count($results),
                'failed' => count($errors)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Bulk operation failed', 500, $e->getMessage());
        }
    }

    // ================================
    // HELPER METHODS
    // ================================

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
            if (!$service) {
                return $this->apiError($response, 'Service not found', 404);
            }

            if (!$user || !$service->can('update', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot update service', 403);
            }

            $service->active = $status;
            $service->save();

            return $this->apiSuccess($response, [
                'message' => "Service {$action} successfully",
                'data' => $service->toArray()
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

    /**
     * REST API: Generate code from name (utility endpoint)
     * POST /api/v1/services/generate-code
     */
    public function generateCode(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            $name = $request->input('name');
            $excludeId = $request->input('exclude_id');

            if (!$name) {
                return $this->apiError($response, 'Name is required to generate code', 400);
            }

            $model = new $this->modelClass;
            if (!$user || !$model->can('read', $user)) {
                return $this->apiError($response, 'Unauthorized: Cannot generate service code', 403);
            }

            $generatedCode = Service::generateCodeFromName($name, $excludeId);

            return $this->apiSuccess($response, [
                'generated_code' => $generatedCode,
                'name' => $name,
                'is_unique' => !Service::codeExists($generatedCode, $excludeId)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to generate code', 500, $e->getMessage());
        }
    }
}
