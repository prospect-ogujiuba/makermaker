<?php
namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceCategoryFields;
use MakerMaker\Models\ServiceCategory;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

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
    public function add()
    {
        $form = tr_form(ServiceCategory::class)->useErrors()->useOld();
        $button = 'Create Category';

        return View::new('service_categories.form', compact('form', 'button'));
    }

    /**
     * Create item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @return mixed
     */
    public function create(ServiceCategoryFields $fields, ServiceCategory $category, Response $response)
    {
        if (!$category->can('create')) {
            $response->unauthorized('Unauthorized: Service Category not created')->abort();
        }

        $category->save($fields);

        return tr_redirect()->toPage('servicecategory', 'index')
            ->withFlash('Service Category Created');
    }

    /**
     * The edit page for admin
     *
     * @param string|ServiceCategory $category
     *
     * @return mixed
     */
    public function edit(ServiceCategory $category)
    {
        $form = tr_form($category)->useErrors()->useOld();
        $button = 'Update Category';

        return View::new('service_categories.form', compact('form', 'button'));
    }

    /**
     * Update item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCategory $category
     *
     * @return mixed
     */
    public function update(ServiceCategory $category, ServiceCategoryFields $fields, Response $response)
    {
        if (!$category->can('update')) {
            $response->unauthorized('Unauthorized: Service Category not updated')->abort();
        }

        $category->save($fields);

        return tr_redirect()->toPage('servicecategory', 'edit', $category->getID())
            ->withFlash('Service Category Updated');
    }

    /**
     * The show page for admin
     *
     * @param string|ServiceCategory $category
     *
     * @return mixed
     */
    public function show(ServiceCategory $category)
    {
        return $category;
    }

    /**
     * The delete page for admin
     *
     * @param string|ServiceCategory $category
     *
     * @return mixed
     */
    public function delete(ServiceCategory $category)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Destroy item
     *
     * AJAX requests and normal requests can be made to this action
     *
     * @param string|ServiceCategory $category
     *
     * @return mixed
     */
    public function destroy(ServiceCategory $category, Response $response)
    {
        if (!$category->can('destroy')) {
            $response->unauthorized('Unauthorized: Service Category not deleted')->abort();
        }

        $category->delete();

        return $response->warning('Service Category Deleted');
    }
}