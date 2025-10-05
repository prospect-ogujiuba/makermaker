<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\ServiceCategoryFields;
use MakerMaker\Models\ServiceCategory;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class ServiceCategoryController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('service_categories.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ServiceCategory::class)->useErrors()->useOld()->useConfirm();
        return View::new('service_categories.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceCategoryFields $fields, ServiceCategory $service_category, Response $response, AuthUser $user)
    {
        if (!$service_category->can('create')) {
            $response->unauthorized('Unauthorized: Service Category not created')->abort();
        }

        autoGenerateCode($fields, 'slug', 'name', '-');

        $service_category->created_by = $user->ID;
        $service_category->updated_by = $user->ID;

        $service_category->save($fields);

        return tr_redirect()->toPage('servicecategory', 'index')
            ->withFlash('Service Type created');
    }

    /**
     * The edit page for admin
     *
     * @param ServiceCategory $service_category
     *
     * @return mixed
     */
    public function edit(ServiceCategory $service_category, AuthUser $user)
    {
        $current_id = $service_category->getID();
        $services = $service_category->services;
        $createdBy = $service_category->createdBy;
        $updatedBy = $service_category->updatedBy;

        $form = tr_form($service_category)->useErrors()->useOld()->useConfirm();
        return View::new('service_categories.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceCategory $service_category
     *
     * @return mixed
     */
    public function update(ServiceCategory $service_category, ServiceCategoryFields $fields, Response $response, AuthUser $user)
    {
        if (!$service_category->can('update')) {
            $response->unauthorized('Unauthorized: Service Category not updated')->abort();
        }

        autoGenerateCode($fields, 'slug', 'name', '-');

        $service_category->updated_by = $user->ID;

        $service_category->save($fields);

        return tr_redirect()->toPage('servicecategory', 'edit', $service_category->getID())
            ->withFlash('Service Type updated');
    }

    /**
     * The show page for admin
     *
     * @param ServiceCategory $service_category
     *
     * @return mixed
     */
    public function show(ServiceCategory $service_category)
    {
        return $service_category->with(['services', 'createdBy', 'updatedBy'])->get();
    }

    /**
     * The delete page for admin
     *
     * @param ServiceCategory $service_category
     *
     * @return mixed
     */
    public function delete(ServiceCategory $service_category)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ServiceCategory $service_category
     *
     * @return mixed
     */
    public function destroy(ServiceCategory $service_category, Response $response)
    {
        if (!$service_category->can('destroy')) {
            return $response->unauthorized('Unauthorized: Service Category not deleted');
        }

        $service_count = $service_category->services()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this.")
                ->setStatus(409)
                ->setData('service_category', $service_category);
        }

        $deleted = $service_category->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Service Category deleted.')->setData('service_category', $service_category);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $service_categories = ServiceCategory::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($service_categories)) {
                return $response
                    ->setData('service_categories', [])
                    ->setMessage('No Service Categories found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('service_categories', $service_categories)
                ->setMessage('Service Categories retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Category indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Service Categories: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ServiceCategory $service_category
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ServiceCategory $service_category, Response $response)
    {
        try {
            $service_category = ServiceCategory::new()
                ->with(['services', 'createdBy', 'updatedBy'])
                ->find($service_category->getID());

            if (empty($service_category)) {
                return $response
                    ->setData('service_category', null)
                    ->setMessage('Service Category not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('service_category', $service_category)
                ->setMessage('Service Category retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Service Category showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Service Category', 'error')
                ->setStatus(500);
        }
    }
}
