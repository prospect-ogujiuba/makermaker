<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceFields;
use MakerMaker\Models\Service;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

class ServiceController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('services.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add()
    {
        $form = tr_form(Service::class)->useErrors()->useOld();
        $button = 'Create Service';

        return View::new('services.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceFields $fields, Service $service, Response $response)
    {
         if (!$service->can('create')) {
            $response->unauthorized('Unauthorized: Service not created')->abort();
        }

        $service->save($fields);

        return tr_redirect()->toPage('service', 'index')
            ->withFlash('Service Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function edit(Service $service)
    {
        $form = tr_form($service)->useErrors()->useOld();
        $button = 'Update Service';

        return View::new('services.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function update(Service $service, ServiceFields $fields, Response $response)
    {
         if (!$service->can('update')) {
            $response->unauthorized('Unauthorized: Service not updated')->abort();
        }

        $service->save($fields);

        return tr_redirect()->toPage('service', 'edit', $service->getID())
            ->withFlash('Service Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function show(Service $service)
    {
        return $service;
    }

    /**
     * The delete page for admin
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function delete(Service $service)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|Service $service
     *
     * @return mixed
     */
    public function destroy(Service $service, Response $response)
    {
        if (!$service->can('destroy')) {
            $response->unauthorized('Unauthorized: Service not deleted')->abort();
        }

        $service->delete();

        return $response->warning('Service Deleted');
    }

    /**
     * REST API: Get all services
     * GET /api/v1/services
     */
    public function indexRest()
    {
        try {
            $services = Service::new()
                ->where('deleted_at', null) // Only active services
                ->findAll()
                ->get();

            return [
                'success' => true,
                'data' => $services,
                'meta' => [
                    'total' => count($services),
                    'timestamp' => date('c')
                ]
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
            $request = tr_request();
            $data = $request->getDataPost();

            // Validate required fields
            $required = ['code', 'name', 'description', 'base_price', 'icon', 'active'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    http_response_code(400);
                    return [
                        'success' => false,
                        'error' => "Field '{$field}' is required"
                    ];
                }
            }

            // Check if code already exists
            $existing = Service::new()->where('code', $data['code'])->take();
            if ($existing) {
                http_response_code(409);
                return [
                    'success' => false,
                    'error' => 'Service code already exists'
                ];
            }

            $service = new Service();
            
            // Check authorization
            if (!$service->can('create')) {
                http_response_code(403);
                return [
                    'success' => false,
                    'error' => 'Unauthorized to create services'
                ];
            }

            $service->save($data);

            http_response_code(201);
            return [
                'success' => true,
                'data' => $service,
                'message' => 'Service created successfully'
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
     * REST API: Update existing service
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
                    'error' => 'Unauthorized to update this service'
                ];
            }

            $request = tr_request();
            $data = $request->getDataPost();

            // If updating code, check for uniqueness
            if (isset($data['code']) && $data['code'] !== $service->code) {
                $existing = Service::new()->where('code', $data['code'])->take();
                if ($existing) {
                    http_response_code(409);
                    return [
                        'success' => false,
                        'error' => 'Service code already exists'
                    ];
                }
            }

            $service->save($data);

            return [
                'success' => true,
                'data' => $service,
                'message' => 'Service updated successfully'
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
                    'error' => 'Unauthorized to delete this service'
                ];
            }

            $service->delete(); // This should use soft delete based on your model

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
     * REST API: Get services with filtering and pagination
     * GET /api/v1/services/search
     */
    public function searchRest()
    {
        try {
            $request = tr_request();
            $params = $request->getDataGet();

            $query = Service::new()->where('deleted_at', null);

            // Apply filters
            if (!empty($params['active'])) {
                $query->where('active', $params['active']);
            }

            if (!empty($params['code'])) {
                $query->where('code', 'LIKE', '%' . $params['code'] . '%');
            }

            if (!empty($params['name'])) {
                $query->where('name', 'LIKE', '%' . $params['name'] . '%');
            }

            if (!empty($params['min_price'])) {
                $query->where('base_price', '>=', $params['min_price']);
            }

            if (!empty($params['max_price'])) {
                $query->where('base_price', '<=', $params['max_price']);
            }

            // Pagination
            $page = $params['page'] ?? 1;
            $limit = $params['limit'] ?? 20;
            $offset = ($page - 1) * $limit;

            $services = $query->take($limit, $offset)->get();
            $total = Service::new()->where('deleted_at', null)->count();

            return [
                'success' => true,
                'data' => $services,
                'meta' => [
                    'total' => $total,
                    'page' => (int)$page,
                    'limit' => (int)$limit,
                    'pages' => ceil($total / $limit)
                ]
            ];

        } catch (\Exception $e) {
            http_response_code(500);
            return [
                'success' => false,
                'error' => 'Search failed',
                'message' => $e->getMessage()
            ];
        }
    }
}
