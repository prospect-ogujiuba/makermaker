<?php

namespace MakerMaker\Controllers\Traits;

use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

/**
 * ApiAuthorization Trait
 * 
 * Provides authorization checking methods for API controllers.
 * Standardizes permission checks across all endpoints.
 */
trait ApiAuthorization
{
    /**
     * Check if user can perform action on model class
     * 
     * @param AuthUser|null $user The authenticated user
     * @param string $action The action to check (create, read, update, delete)
     * @param string|null $modelClass The model class to check against
     * @return bool True if authorized, false otherwise
     */
    protected function canPerformAction(?AuthUser $user, string $action, ?string $modelClass = null): bool
    {
        if (!$user) {
            return false;
        }

        $modelClass = $modelClass ?? $this->modelClass ?? null;
        
        if (!$modelClass) {
            return false;
        }

        $model = new $modelClass;
        return $model->can($action, $user);
    }

    /**
     * Check if user can perform action on specific model instance
     * 
     * @param AuthUser|null $user The authenticated user
     * @param string $action The action to check
     * @param mixed $model The model instance
     * @return bool True if authorized, false otherwise
     */
    protected function canPerformActionOnModel(?AuthUser $user, string $action, $model): bool
    {
        if (!$user || !$model) {
            return false;
        }

        return $model->can($action, $user);
    }

    /**
     * Require authentication and return error response if not authenticated
     * 
     * @param Response $response The response object
     * @param AuthUser|null $user The authenticated user
     * @param string $action The action being attempted
     * @param string $resource The resource name
     * @return Response|null Returns error response if unauthorized, null if authorized
     */
    protected function requireAuth(Response $response, ?AuthUser $user, string $action = 'perform this action', string $resource = 'resource'): ?Response
    {
        if (!$user) {
            return $this->apiUnauthorized($response, $action, $resource);
        }

        return null;
    }

    /**
     * Require permission and return error response if not authorized
     * 
     * @param Response $response The response object
     * @param AuthUser|null $user The authenticated user
     * @param string $action The action to check
     * @param string $resource The resource name
     * @param string|null $modelClass The model class
     * @return Response|null Returns error response if unauthorized, null if authorized
     */
    protected function requirePermission(Response $response, ?AuthUser $user, string $action, string $resource = 'resource', ?string $modelClass = null): ?Response
    {
        if (!$this->canPerformAction($user, $action, $modelClass)) {
            return $this->apiUnauthorized($response, $action, $resource);
        }

        return null;
    }

    /**
     * Require permission on specific model and return error response if not authorized
     * 
     * @param Response $response The response object
     * @param AuthUser|null $user The authenticated user
     * @param string $action The action to check
     * @param mixed $model The model instance
     * @param string $resource The resource name
     * @return Response|null Returns error response if unauthorized, null if authorized
     */
    protected function requireModelPermission(Response $response, ?AuthUser $user, string $action, $model, string $resource = 'resource'): ?Response
    {
        if (!$this->canPerformActionOnModel($user, $action, $model)) {
            return $this->apiUnauthorized($response, $action, $resource);
        }

        return null;
    }

    /**
     * Check if user is authenticated
     * 
     * @param AuthUser|null $user The authenticated user
     * @return bool True if authenticated, false otherwise
     */
    protected function isAuthenticated(?AuthUser $user): bool
    {
        return $user !== null;
    }

    /**
     * Get the model class for authorization checks
     * Override this method in controllers if $modelClass property is not set
     * 
     * @return string|null The model class name
     */
    protected function getModelClass(): ?string
    {
        return $this->modelClass ?? null;
    }
}