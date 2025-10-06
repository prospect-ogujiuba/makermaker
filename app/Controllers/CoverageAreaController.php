<?php

namespace MakerMaker\Controllers;

use MakerMaker\Http\Fields\CoverageAreaFields;
use MakerMaker\Models\CoverageArea;
use MakerMaker\View;
use TypeRocket\Controllers\Controller;
use TypeRocket\Http\Response;
use TypeRocket\Models\AuthUser;

class CoverageAreaController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        return View::new('coverage_areas.index');
    }

    /**
     * The add page for admin
     *
     * @return mixed
     */
    public function add(AuthUser $user)
    {
        $form = tr_form(CoverageArea::class)->useErrors()->useOld()->useConfirm();
        return View::new('coverage_areas.form', compact('form', 'user'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(CoverageAreaFields $fields, CoverageArea $coverage_area, Response $response, AuthUser $user)
    {
        if (!$coverage_area->can('create')) {
            $response->unauthorized('Unauthorized: Coverage Area not created')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $coverage_area->created_by = $user->ID;
        $coverage_area->updated_by = $user->ID;

        $coverage_area->save($fields);

        return tr_redirect()->toPage('coveragearea', 'index')
            ->withFlash('Coverage Area created');
    }

    /**
     * The edit page for admin
     *
     * @param CoverageArea $coverage_area
     *
     * @return mixed
     */
    public function edit(CoverageArea $coverage_area, AuthUser $user)
    {
        $current_id = $coverage_area->getID();
        $serviceCoverages = $coverage_area->serviceCoverages;
        $createdBy = $coverage_area->createdBy;
        $updatedBy = $coverage_area->updatedBy;

        $form = tr_form($coverage_area)->useErrors()->useOld()->useConfirm();
        return View::new('coverage_areas.form', compact('form', 'current_id', 'serviceCoverages', 'createdBy', 'updatedBy', 'user'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param CoverageArea $coverage_area
     *
     * @return mixed
     */
    public function update(CoverageArea $coverage_area, CoverageAreaFields $fields, Response $response, AuthUser $user)
    {
        if (!$coverage_area->can('update')) {
            $response->unauthorized('Unauthorized: Coverage Area not updated')->abort();
        }

        autoGenerateCode($fields, 'code', 'name');
        $fields['code'] = mm_kebab($fields['code']);

        $coverage_area->updated_by = $user->ID;

        $coverage_area->save($fields);

        return tr_redirect()->toPage('coveragearea', 'edit', $coverage_area->getID())
            ->withFlash('Coverage Area updated');
    }

    /**
     * The show page for admin
     *
     * @param CoverageArea $coverage_area
     *
     * @return mixed
     */
    public function show(CoverageArea $coverage_area)
    {
        return $coverage_area;
    }

    /**
     * The delete page for admin
     *
     * @param CoverageArea $coverage_area
     *
     * @return mixed
     */
    public function delete(CoverageArea $coverage_area)
    {
        //
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param CoverageArea $coverage_area
     *
     * @return mixed
     */
    public function destroy(CoverageArea $coverage_area, Response $response)
    {
        if (!$coverage_area->can('destroy')) {
            return $response->unauthorized('Unauthorized: Coverage Area not deleted');
        }

        $serviceCoveragesCount = $coverage_area->serviceCoverages()->count();

        if ($serviceCoveragesCount > 0) {
            return $response
                ->error("Cannot delete: {$serviceCoveragesCount} Service Coverage(s) still use this Coverage Area.")
                ->setStatus(409)
                ->setData('coverage_area', $coverage_area);
        }

        $deleted = $coverage_area->delete();

        if ($deleted === false) {
            return $response
                ->error('Delete failed due to a database error.')
                ->setStatus(500);
        }

        return $response->success('Coverage Area deleted.')->setData('coverage_area', $coverage_area);
    }

    /**
     * The index function for API
     *
     * @return \TypeRocket\Http\Response
     */
    public function indexRest(Response $response)
    {
        try {
            $coverage_areas = CoverageArea::new()
                ->with(['serviceCoverages', 'createdBy', 'updatedBy'])
                ->get();

            if (empty($coverage_areas)) {
                return $response
                    ->setData('coverage_areas', [])
                    ->setMessage('No Coverage Areas found', 'info')
                    ->setStatus(200);
            }

            return $response
                ->setData('coverage_areas', $coverage_areas)
                ->setMessage('Coverage Areas retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('Coverage Area indexRest error: ' . $e->getMessage());

            return $response
                ->error('Failed to retrieve Coverage Areas: ' . $e->getMessage())
                ->setStatus(500);
        }
    }

    /**
     * The show function for API
     *
     * @param CoverageArea $coverage_area
     * @param Response $response
     *
     * @return \TypeRocket\Http\Response
     */
    public function showRest(CoverageArea $coverage_area, Response $response)
    {
        try {
            $coverage_area = CoverageArea::new()
                ->with(['serviceCoverages', 'createdBy', 'updatedBy'])
                ->find($coverage_area->getID());

            if (empty($coverage_area)) {
                return $response
                    ->setData('coverage_area', null)
                    ->setMessage('Coverage Area not found', 'info')
                    ->setStatus(404);
            }

            return $response
                ->setData('coverage_area', $coverage_area)
                ->setMessage('Coverage Area retrieved successfully', 'success')
                ->setStatus(200);
        } catch (\Exception $e) {
            error_log('CoverageArea showRest error: ' . $e->getMessage());
            return $response
                ->setMessage('An error occurred while retrieving Coverage Area', 'error')
                ->setStatus(500);
        }
    }
}
