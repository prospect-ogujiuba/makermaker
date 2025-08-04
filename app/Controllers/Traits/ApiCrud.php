<?php

namespace MakerMaker\Controllers\Traits;

use TypeRocket\Http\Request;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;
use TypeRocket\Exceptions\ModelException;

/**
 * ApiCrud Trait
 * 
 * Provides generic CRUD operations for API controllers.
 * Can be used with any model that follows TypeRocket patterns.
 */
trait ApiCrud
{
    use ApiResponses, ApiAuthorization, ApiRequestHandling;

    /**
     * Get the resource name for responses (e.g., 'service', 'customer')
     * Override in controller if different from model class
     * 
     * @return string
     */
    protected function getResourceName(): string
    {
        $modelClass = $this->getModelClass();
        return $modelClass ? strtolower(class_basename($modelClass)) : 'resource';
    }

    /**
     * Get the plural resource name for responses (e.g., 'services', 'customers')
     * Override in controller if different from adding 's'
     * 
     * @return string
     */
    protected function getPluralResourceName(): string
    {
        return $this->getResourceName() . 's';
    }

    /**
     * Generic index method - Get all records (excludes soft deleted by default)
     * 
     * @param Request $request
     * @param Response $response
     * @param AuthUser|null $user
     * @return Response
     */
    protected function indexGeneric(Request $request, Response $response, ?AuthUser $user = null): Response
    {
        try {

            $modelClass = $this->getModelClass();
            $query = $modelClass::new();

            // Apply soft delete filtering if trait exists
            if (method_exists($query, 'withoutDeleted')) {
                $includeDeleted = $request->input('include_deleted') === 'true';
                if (!$includeDeleted) {
                    $query = $query->withoutDeleted();
                }
            }

            // Apply active filtering if requested and trait exists
            $activeFilter = $request->input('active');
            if ($activeFilter !== null && method_exists($query, 'active')) {
                $query = $activeFilter === 'true' ? $query->active() : $query->inactive();
            }

            // Get pagination parameters
            $page = max(1, (int) ($request->input('page') ?? 1));
            $limit = min(100, max(1, (int) ($request->input('limit') ?? 20)));

            // Apply pagination
            $pager = $query->paginate($page, $limit);
            $items = $pager->getItems();

            return $this->apiPaginated($response, $items->toArray(), [
                'current_page' => $pager->getCurrentPage(),
                'per_page' => $pager->getNumberPerPage(),
                'total' => $pager->getCount(),
                'total_pages' => $pager->getNumberOfPages(),
                'has_more' => $pager->getCurrentPage() < $pager->getNumberOfPages()
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, "Failed to retrieve {$this->getPluralResourceName()}", 500, $e->getMessage());
        }
    }

    /**
     * Generic show method - Get single record by ID
     * 
     * @param mixed $id
     * @param Request $request
     * @param Response $response
     * @param AuthUser|null $user
     * @return Response
     */
    protected function showGeneric($id, Request $request, Response $response, ?AuthUser $user = null): Response
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'ID is required', 400);
            }

            $modelClass = $this->getModelClass();
            $item = $modelClass::new()->findById($id);

            if (!$item) {
                return $this->apiNotFound($response, ucfirst($this->getResourceName()));
            }

            return $this->apiSuccess($response, [
                $this->getResourceName() => $item->toArray()
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, "Failed to retrieve {$this->getResourceName()}", 500, $e->getMessage());
        }
    }

    /**
     * Generic search method - Search records by query
     * 
     * @param string $query
     * @param Request $request
     * @param Response $response
     * @param AuthUser|null $user
     * @return Response
     */
    protected function searchGeneric($query, Request $request, Response $response, ?AuthUser $user = null): Response
    {
        try {
            if (!$query) {
                return $this->apiError($response, 'Search query is required', 400);
            }

            $modelClass = $this->getModelClass();
            $searchQuery = $modelClass::new();

            // Apply soft delete filtering if trait exists
            if (method_exists($searchQuery, 'withoutDeleted')) {
                $searchQuery = $searchQuery->withoutDeleted();
            }

            // Search in common fields - override getSearchFields() to customize
            $searchFields = $this->getSearchFields();
            $searchQuery = $this->applySearchFilters($searchQuery, $query, $searchFields);

            $results = $searchQuery->get();

            return $this->apiSuccess($response, [
                'data' => $results->toArray(),
                'query' => $query
            ]);
        } catch (\Exception $e) {
            return $this->apiError($response, "Failed to search {$this->getPluralResourceName()}", 500, $e->getMessage());
        }
    }

    /**
     * Get fields to search in - override in controller to customize
     * 
     * @return array
     */
    protected function getSearchFields(): array
    {
        return ['name', 'code', 'description'];
    }

    /**
     * Apply search filters to query
     * 
     * @param mixed $query
     * @param string $searchTerm
     * @param array $fields
     * @return mixed
     */
    protected function applySearchFilters($query, string $searchTerm, array $fields)
    {
        $isFirst = true;
        foreach ($fields as $field) {
            if ($isFirst) {
                $query = $query->where($field, 'LIKE', "%{$searchTerm}%");
                $isFirst = false;
            } else {
                $query = $query->orWhere($field, 'LIKE', "%{$searchTerm}%");
            }
        }
        
        return $query;
    }

    /**
     * Generic destroy method - Soft delete record
     * 
     * @param mixed $id
     * @param Request $request
     * @param Response $response
     * @param AuthUser|null $user
     * @return Response
     */
    protected function destroyGeneric($id, Request $request, Response $response, ?AuthUser $user = null): Response
    {
        try {
            if (!$id) {
                return $this->apiError($response, 'ID is required', 400);
            }

            $modelClass = $this->getModelClass();
            $item = $modelClass::new()->findById($id);

            if (!$item) {
                return $this->apiNotFound($response, ucfirst($this->getResourceName()));
            }

            // Check authorization
            if ($authResponse = $this->requireModelPermission($response, $user, 'destroy', $item, $this->getResourceName())) {
                return $authResponse;
            }

            // Use soft delete if available, otherwise hard delete
            if (method_exists($item, 'softDelete')) {
                $item->softDelete();
                $message = ucfirst($this->getResourceName()) . ' deleted successfully (soft delete)';
            } else {
                $item->delete();
                $message = ucfirst($this->getResourceName()) . ' deleted successfully';
            }

            return $this->apiDeleted($response, $message);
        } catch (\Exception $e) {
            return $this->apiError($response, "Failed to delete {$this->getResourceName()}", 500, $e->getMessage());
        }
    }
}