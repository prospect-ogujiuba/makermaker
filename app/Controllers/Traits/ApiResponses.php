<?php

namespace MakerMaker\Controllers\Traits;

use TypeRocket\Http\Response;

/**
 * ApiResponses Trait
 * 
 * Provides standardized API response methods for controllers.
 * Ensures consistent response formats across all API endpoints.
 */
trait ApiResponses
{
    /**
     * Helper method for API success responses
     * 
     * @param Response $response The response object
     * @param array $data Data to include in response
     * @param int $status HTTP status code (default: 200)
     * @return Response
     */
    protected function apiSuccess(Response $response, array $data = [], int $status = 200): Response
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
     * 
     * @param Response $response The response object
     * @param string $message Error message
     * @param int $status HTTP status code (default: 400)
     * @param mixed $details Additional error details
     * @return Response
     */
    protected function apiError(Response $response, string $message, int $status = 400, $details = null): Response
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
     * Helper method for API validation error responses
     * 
     * @param Response $response The response object
     * @param array $errors Validation errors array
     * @param string $message Optional custom message
     * @return Response
     */
    protected function apiValidationError(Response $response, array $errors, string $message = 'Validation failed'): Response
    {
        return $this->apiError($response, $message, 422, $errors);
    }

    /**
     * Helper method for API not found responses
     * 
     * @param Response $response The response object
     * @param string $resource Resource name (e.g., 'Service', 'User')
     * @return Response
     */
    protected function apiNotFound(Response $response, string $resource = 'Resource'): Response
    {
        return $this->apiError($response, "{$resource} not found", 404);
    }

    /**
     * Helper method for API unauthorized responses
     * 
     * @param Response $response The response object
     * @param string $action Action being attempted (e.g., 'create', 'update')
     * @param string $resource Resource name (e.g., 'service', 'user')
     * @return Response
     */
    protected function apiUnauthorized(Response $response, string $action = 'perform this action', string $resource = 'resource'): Response
    {
        return $this->apiError($response, "Unauthorized: Cannot {$action} {$resource}", 403);
    }

    /**
     * Helper method for API created responses
     * 
     * @param Response $response The response object
     * @param array $data Data to include in response
     * @param string $message Success message
     * @return Response
     */
    protected function apiCreated(Response $response, array $data = [], string $message = 'Resource created successfully'): Response
    {
        $response->setMessage($message);
        return $this->apiSuccess($response, $data, 201);
    }

    /**
     * Helper method for API updated responses
     * 
     * @param Response $response The response object
     * @param array $data Data to include in response
     * @param string $message Success message
     * @return Response
     */
    protected function apiUpdated(Response $response, array $data = [], string $message = 'Resource updated successfully'): Response
    {
        $response->setMessage($message);
        return $this->apiSuccess($response, $data, 200);
    }

    /**
     * Helper method for API deleted responses
     * 
     * @param Response $response The response object
     * @param string $message Success message
     * @return Response
     */
    protected function apiDeleted(Response $response, string $message = 'Resource deleted successfully'): Response
    {
        return $this->apiSuccess($response, ['message' => $message], 200);
    }

    /**
     * Helper method for paginated API responses
     * 
     * @param Response $response The response object
     * @param array $data Main data array
     * @param array $pagination Pagination info
     * @return Response
     */
    protected function apiPaginated(Response $response, array $data, array $pagination): Response
    {
        return $this->apiSuccess($response, [
            'data' => $data,
            'pagination' => $pagination
        ]);
    }
}