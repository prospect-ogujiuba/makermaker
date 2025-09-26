<?php

namespace MakerMaker\Controllers;

use MakerMaker\Models\ComplexityLevel;
use MakerMaker\Http\Fields\ComplexityLevelFields;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;
use TypeRocket\Models\AuthUser;

class ComplexityLevelController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('complexity_levels.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(ComplexityLevel::class)->useErrors()->useOld()->useConfirm();
        return View::new('complexity_levels.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ComplexityLevelFields $fields, ComplexityLevel $complexity_level, Response $response, AuthUser $user)
    {
        if (!$complexity_level->can('create')) {
            $response->unauthorized('Unauthorized: Complexity Level not created')->abort();
        }

        $complexity_level->created_by = $user->ID;
        $complexity_level->updated_by = $user->ID;

        $complexity_level->save($fields);

        return tr_redirect()->toPage('complexitylevel', 'index')
            ->withFlash('Complexity Level created');
    }

    /**
     * Edit item
     *
     * @param string|ComplexityLevel $complexity_level
     *
     * @return mixed
     */
    public function edit(ComplexityLevel $complexity_level, AuthUser $user)
    {
        $current_id = $complexity_level->getID();
        $services = $complexity_level->services;
        $createdBy = $complexity_level->createdBy;
        $updatedBy = $complexity_level->updatedBy;

        $form = tr_form($complexity_level)->useErrors()->useOld()->useConfirm();
        return View::new('complexity_levels.form', compact('form', 'current_id', 'services', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ComplexityLevel $complexity_level
     *
     * @return mixed
     */
    public function update(ComplexityLevel $complexity_level, ComplexityLevelFields $fields, Response $response, AuthUser $user)
    {
        if (!$complexity_level->can('update')) {
            $response->unauthorized('Unauthorized: Complexity Level not updated')->abort();
        }

        $complexity_level->updated_by = $user->ID;

        $complexity_level->save($fields);

        return tr_redirect()->toPage('complexitylevel', 'edit', $complexity_level->getID())
            ->withFlash('Complexity Level updated');
    }

    /**
     * The show page for admin
     *
     * @param ComplexityLevel $complexity_level
     *
     * @return mixed
     */
    public function show(ComplexityLevel $complexity_level)
    {
        return $complexity_level;
    }

    /**
     * The delete page for admin
     *
     * @param ComplexityLevel $complexity_level
     *
     * @return mixed
     */
    public function delete(ComplexityLevel $complexity_level)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param ComplexityLevel $complexity_level
     *
     * @return mixed
     */
    public function destroy(ComplexityLevel $complexity_level, Response $response)
    {
        if (!$complexity_level->can('destroy')) {
            return $response->unauthorized('Unauthorized: Complexity Level not deleted');
        }

        // Check if this complexity is still being used by services
        $service_count = $complexity_level->services()->count();

        if ($service_count > 0) {
            return $response
                ->error("Cannot delete: {$service_count} service(s) still use this Complexity Level.")
                ->setStatus(409)
                ->setData('complexity_level', $complexity_level);
        }

        $deleted = $complexity_level->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Complexity Level deleted.')->setData('complexity_level', $complexity_level);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $complexity_levels = ComplexityLevel::new()->get();

            if (empty($complexity_levels)) {
                return $response
                    ->setData('complexity_levels', [])
                    ->setMessage('No Complexity Levels found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('complexity_levels', $complexity_levels)
                ->setMessage('Complexity Levels retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Complexity Level indexRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Complexity Levels', 'error')
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param ComplexityLevel $complexity_level
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(ComplexityLevel $complexity_level, Response $response)
    {
        try {
            $complexity_level = ComplexityLevel::new()
                ->find($complexity_level->getID());

            if (empty($complexity_level)) {
                return $response
                    ->setData('complexity_level', null)
                    ->setMessage('Complexity Level not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('complexity_level', $complexity_level)
                ->setMessage('Complexity Level retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Complexity Level showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Complexity Level', 'error')
                ->setStatus(500);
        }
    }
}
