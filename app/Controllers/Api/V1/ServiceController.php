<?php

namespace MakerMaker\Controllers\Api\V1;

use MakerMaker\Controllers\Web\ServiceController as WebServiceController;
use MakerMaker\Controllers\Traits\ApiCrud;
use MakerMaker\Http\Fields\ServiceFields;
use MakerMaker\Models\Service;
use MakerMaker\Helpers\ApiHelpers;
use TypeRocket\Controllers\Controller;
use TypeRocket\Exceptions\ModelException;
use TypeRocket\Http\Request;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceController extends Controller
{
    use ApiCrud;

    protected $modelClass = Service::class;

    // ================================
    // STANDARD CRUD OPERATIONS (using traits)
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

            if (($active = $request->input('active')) !== null) {
                $activeValue = strtolower($active);
                if (in_array($activeValue, ['true', '1', 'yes'])) {
                    $query = $query->where('active', '=', 1);
                } elseif (in_array($activeValue, ['false', '0', 'no'])) {
                    $query = $query->where('active', '=', 0);
                }
                // If invalid value, ignore the filter instead of erroring
            }

            // Individual field filters
            if ($name = $request->input('name')) {
                $query = $query->where('name', 'LIKE', "%{$name}%");
            }

            if ($code = $request->input('code')) {
                $query = $query->where('code', 'LIKE', "%{$code}%");
            }

            if ($description = $request->input('description')) {
                $query = $query->where('description', 'LIKE', "%{$description}%");
            }

            // Price filters
            if ($priceMin = $request->input('price_min')) {
                if (is_numeric($priceMin)) {
                    $query = $query->where('base_price', '>=', (float) $priceMin);
                }
            }

            if ($priceMax = $request->input('price_max')) {
                if (is_numeric($priceMax)) {
                    $query = $query->where('base_price', '<=', (float) $priceMax);
                }
            }

            if ($pricingType = $request->input('pricing_type')) {
                if ($pricingType === 'has_price') {
                    $query = $query->where('base_price', '>', 0)->where('base_price', '!=', null);
                } elseif ($pricingType === 'quote_required') {
                    $query = $query->where(function ($q) {
                        return $q->where('base_price', '=', 0)->orWhere('base_price', '=', null);
                    });
                }
            }

            // Category filter
            if ($categoryFilter = $request->input('category_filter')) {
                $query = $query->where('name', '=', $categoryFilter);
            }

            // Date filters
            if ($createdFrom = $request->input('created_from')) {
                $query = $query->where('created_at', '>=', $createdFrom . ' 00:00:00');
            }

            if ($createdTo = $request->input('created_to')) {
                $query = $query->where('created_at', '<=', $createdTo . ' 23:59:59');
            }

            if ($updatedFrom = $request->input('updated_from')) {
                $query = $query->where('updated_at', '>=', $updatedFrom . ' 00:00:00');
            }

            if ($updatedTo = $request->input('updated_to')) {
                $query = $query->where('updated_at', '<=', $updatedTo . ' 23:59:59');
            }

            // Service ID filter
            if ($serviceId = $request->input('service_id')) {
                if (is_numeric($serviceId)) {
                    $query = $query->where('id', '=', (int) $serviceId);
                }
            }

            // Keywords search
            if ($keywords = $request->input('keywords')) {
                $query = $query->where(function ($q) use ($keywords) {
                    return $q->where('name', 'LIKE', "%{$keywords}%")
                        ->orWhere('code', 'LIKE', "%{$keywords}%")
                        ->orWhere('description', 'LIKE', "%{$keywords}%");
                });
            }

            // Sorting
            $sortBy = $request->input('sort_by', 'id');
            $sortOrder = $request->input('sort_order', 'asc');

            $allowedSortColumns = ['id', 'name', 'code', 'description', 'base_price', 'active', 'created_at', 'updated_at'];
            if (in_array($sortBy, $allowedSortColumns) && in_array($sortOrder, ['asc', 'desc'])) {
                $query = $query->orderBy($sortBy, $sortOrder);
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
        return $this->showGeneric($id, $request, $response, $user);
    }

    /**
     * REST API: Search services
     * GET /api/v1/services/search/{query}
     */
    public function search($query = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        return $this->searchGeneric($query, $request, $response, $user);
    }

    /**
     * REST API: Soft delete service
     * DELETE /api/v1/services/{id}
     */
    public function destroy($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        return $this->destroyGeneric($id, $request, $response, $user);
    }

    // ================================
    // CUSTOM SERVICE-SPECIFIC OPERATIONS
    // ================================

    /**
     * REST API: Create new service
     * POST /api/v1/services
     */
    public function create(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            // Check authorization
            if ($authResponse = $this->requirePermission($response, $user, 'create', 'service')) {
                return $authResponse;
            }

            // Parse and validate input data
            $inputData = $this->parseRequestData($request);
            $validationErrors = $this->validateRequiredFields($inputData, $this->getRequiredFields());

            if (!empty($validationErrors)) {
                return $this->apiValidationError($response, $validationErrors);
            }

            // Sanitize input data
            $inputData = $this->sanitizeData($inputData);

            // Auto-generate code if not provided but name is available
            if (empty($inputData['code']) && !empty($inputData['name'])) {
                $inputData['code'] = Service::generateCodeFromName($inputData['name']);
            }

            // Check for duplicate service code (if code was provided or generated)
            if (isset($inputData['code']) && Service::codeExists($inputData['code'])) {
                return $this->apiError($response, 'Service with this code already exists', 409);
            }

            // Use TypeRocket's existing ServiceFields and WebController for consistency
            $fields = new ServiceFields($inputData);
            $controller = new WebServiceController();
            $controller->create($fields, new Service(), $response);

            return $this->apiCreated($response, [
                'service' => $fields
            ], 'Service created successfully');
        } catch (ModelException $e) {
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
                return $this->apiNotFound($response, 'Service');
            }

            // Check authorization
            if ($authResponse = $this->requireModelPermission($response, $user, 'update', $service, 'service')) {
                return $authResponse;
            }

            // Parse and sanitize input data
            $inputData = $this->parseRequestData($request);
            $inputData = $this->sanitizeData($inputData);

            // Auto-generate code if not provided but name is being updated
            if (empty($inputData['code']) && !empty($inputData['name'])) {
                $inputData['code'] = Service::generateCodeFromName($inputData['name'], $id);
            }

            // Check for duplicate service code (excluding current service)
            if (isset($inputData['code']) && $inputData['code'] !== $service->code) {
                if (Service::codeExists($inputData['code'], $id)) {
                    return $this->apiError($response, 'Service with this code already exists', 409);
                }
            }

            // Use TypeRocket's existing ServiceFields and WebController
            $fields = new ServiceFields($inputData);
            $controller = new WebServiceController();
            $controller->update($service, $fields, $response);

            return $this->apiUpdated($response, [
                'service' => $service->toArray()
            ], 'Service updated successfully');
        } catch (ModelException $e) {
            return $this->apiError($response, 'Failed to update service', 400, $e->getMessage());
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to update service', 500, $e->getMessage());
        }
    }

    /**
     * REST API: Partial update service (PATCH)
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
                return $this->apiNotFound($response, 'Service');
            }

            // Check authorization
            if ($authResponse = $this->requireModelPermission($response, $user, 'update', $service, 'service')) {
                return $authResponse;
            }

            // Parse and validate PATCH data
            $inputData = $this->parseRequestData($request);
            $validationErrors = $this->validatePatchData($inputData, $this->getAllowedFields());

            if (!empty($validationErrors)) {
                return $this->apiValidationError($response, $validationErrors);
            }

            // Sanitize and filter to allowed fields only
            $inputData = $this->sanitizeData($inputData);
            $inputData = $this->filterAllowedFields($inputData, $this->getAllowedFields());

            // Update only provided fields
            foreach ($inputData as $field => $value) {
                $service->$field = $value;
            }

            $service->save();

            return $this->apiUpdated($response, [
                'service' => $service->toArray()
            ], 'Service updated successfully');
        } catch (\Exception $e) {
            return $this->apiError($response, 'Failed to patch service', 500, $e->getMessage());
        }
    }

    // ================================
    // SERVICE-SPECIFIC LOOKUP ENDPOINTS
    // ================================

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
                return $this->apiNotFound($response, 'Service');
            }

            // Check authorization
            if ($authResponse = $this->requireModelPermission($response, $user, 'read', $service, 'service')) {
                return $authResponse;
            }

            return $this->apiSuccess($response, [
                'service' => $service->toArray()
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
        return $this->toggleStatus($id, true, 'activate', $request, $response, $user);
    }

    /**
     * REST API: Deactivate service
     * POST /api/v1/services/{id}/deactivate
     */
    public function deactivate($id = null, Request $request, Response $response, ?AuthUser $user = null)
    {
        return $this->toggleStatus($id, false, 'deactivate', $request, $response, $user);
    }

    /**
     * Toggle service status (activate/deactivate)
     */
    private function toggleStatus($id, bool $status, string $action, Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'Service ID is required', 400);
            }

            $service = Service::new()->findById($id);
            if (!$service) {
                return $this->apiNotFound($response, 'Service');
            }

            // Check authorization
            if ($authResponse = $this->requireModelPermission($response, $user, 'update', $service, 'service')) {
                return $authResponse;
            }

            // Use trait method for consistent status management
            if ($status) {
                $service->activate();
            } else {
                $service->deactivate();
            }

            return $this->apiSuccess($response, [
                'message' => "Service {$action}d successfully",
                'service' => $service->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, "Failed to {$action} service", 500, $e->getMessage());
        }
    }

    // ================================
    // BULK OPERATIONS
    // ================================

    /**
     * REST API: Bulk operations on services
     * POST /api/v1/services/bulk
     * Body: {"action": "activate|deactivate|delete|restore", "ids": [1,2,3]}
     */
    public function bulk(Request $request, Response $response, ?AuthUser $user = null)
    {
        try {
            $inputData = $this->parseRequestData($request);
            [$action, $ids, $errors] = ApiHelpers::parseBulkOperation($inputData);

            if (!empty($errors)) {
                return $this->apiValidationError($response, $errors);
            }

            // Check authorization
            if ($authResponse = $this->requirePermission($response, $user, 'update', 'services')) {
                return $authResponse;
            }

            $results = [];
            $errorResults = [];

            foreach ($ids as $id) {
                try {
                    $service = Service::new()->findById($id);
                    if (!$service) {
                        $errorResults[] = "Service ID {$id} not found";
                        continue;
                    }

                    // Check individual authorization
                    if (!$this->canPerformActionOnModel($user, 'update', $service)) {
                        $errorResults[] = "Unauthorized to update service ID {$id}";
                        continue;
                    }

                    // Perform action using trait methods
                    switch ($action) {
                        case 'activate':
                            $service->activate();
                            break;
                        case 'deactivate':
                            $service->deactivate();
                            break;
                        case 'delete':
                            $service->softDelete();
                            break;
                        case 'restore':
                            $service->restore();
                            break;
                    }

                    $results[] = $service->toArray();
                } catch (\Exception $e) {
                    $errorResults[] = "Error processing service ID {$id}: " . $e->getMessage();
                }
            }

            return $this->apiSuccess($response, [
                'message' => 'Bulk operation completed',
                'action' => $action,
                'results' => $results,
                'errors' => $errorResults,
                'processed' => count($results),
                'failed' => count($errorResults)
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, 'Bulk operation failed', 500, $e->getMessage());
        }
    }

    // ================================
    // UTILITY ENDPOINTS
    // ================================

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

            // Check authorization
            if ($authResponse = $this->requirePermission($response, $user, 'read', 'services')) {
                return $authResponse;
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

    // ================================
    // TRAIT METHOD OVERRIDES
    // ================================

    /**
     * Override to customize search fields
     */
    protected function getSearchFields(): array
    {
        return ['name', 'code', 'description'];
    }

    /**
     * Override to customize allowed fields for updates
     */
    protected function getAllowedFields(): array
    {
        return ['code', 'name', 'description', 'base_price', 'icon', 'active'];
    }

    /**
     * Override to customize required fields for creation
     */
    protected function getRequiredFields(): array
    {
        return ['name'];
    }

    /**
     * Override to add service-specific validation
     */
    protected function validateField(string $field, $value): array
    {
        // Get base validation errors from trait
        $errors = [];

        // Call trait method directly (no parent::)
        switch ($field) {
            case 'base_price':
            case 'price':
                if (!is_numeric($value) || $value < 0) {
                    $errors[] = "The {$field} must be a positive number.";
                }
                break;

            case 'active':
                if (!in_array($value, ['0', '1', 0, 1, true, false], true)) {
                    $errors[] = "The {$field} field must be true or false.";
                }
                break;

            case 'code':
                if (!preg_match('/^[a-z0-9_]+$/', $value)) {
                    $errors[] = "The {$field} may only contain lowercase letters, numbers, and underscores.";
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "The {$field} must be a valid email address.";
                }
                break;
        }

        // Add service-specific validation
        switch ($field) {
            case 'name':
                if (empty($value) || strlen($value) < 2) {
                    $errors[] = 'Service name must be at least 2 characters long.';
                }
                break;

            case 'base_price':
                if (!is_numeric($value) || $value < 0) {
                    $errors[] = 'Base price must be a positive number.';
                }
                break;

            case 'icon':
                // You could add icon validation here if needed
                break;
        }

        return $errors;
    }

    /**
     * Override resource names for consistent API responses
     */
    protected function getResourceName(): string
    {
        return 'service';
    }

    protected function getPluralResourceName(): string
    {
        return 'services';
    }
}
