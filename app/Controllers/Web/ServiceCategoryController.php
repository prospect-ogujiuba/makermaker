<?php
namespace MakerMaker\Controllers\Web;

use MakerMaker\Http\Fields\ServiceCategoryFields;
use MakerMaker\Models\ServiceCategory;
use TypeRocket\Http\Response;
use TypeRocket\Controllers\Controller;
use MakerMaker\View;

/**
 * ServiceCategoryController
 * 
 * Handles CRUD operations for service categories with hierarchical support
 */
class ServiceCategoryController extends Controller
{
    /**
     * The index page for admin
     *
     * @return mixed
     */
    public function index()
    {
        // Get categories in tree structure for display
        // $categories = ServiceCategory::with(['parent', 'children', 'services'])
        //     ->ordered()
        //     ->get();

        // Build tree structure
        // $categoryTree = ServiceCategory::buildTree();
        
        // Get stats for dashboard
        $stats = [
            // 'total_categories' => ServiceCategory::count(),
            // 'active_categories' => ServiceCategory::active()->count(),
            // 'top_level_categories' => ServiceCategory::topLevel()->count(),
            // 'categories_with_services' => ServiceCategory::whereHas('services')->count()
        ];

        return View::new('service_categories.index', compact('stats'));
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

        // Get parent category options
        $parentOptions = ServiceCategory::getHierarchicalOptions();

        return View::new('service_categories.form', compact('form', 'button', 'parentOptions'));
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

        try {
            $category->save($fields);

            // Handle AJAX vs regular requests
            if ($response->isAjax()) {
                return $response->setData('category', $category->toArray())
                    ->setMessage('Service Category Created')
                    ->setStatus(201);
            }

            return tr_redirect()->toPage('servicecategory', 'index')
                ->withFlash('Service Category Created', 'success');
                
        } catch (\Exception $e) {
            if ($response->isAjax()) {
                return $response->setError('creation_failed', $e->getMessage())
                    ->setStatus(400);
            }

            return tr_redirect()->back()
                ->withFlash('Error creating category: ' . $e->getMessage(), 'error')
                ->withErrors($fields->getErrors());
        }
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

        // Get parent category options (exclude self and descendants to prevent loops)
        $parentOptions = ServiceCategory::getHierarchicalOptions();
        
        // Remove self and descendants from parent options
        $excludeIds = [$category->id];
        $descendants = $category->getDescendants();
        foreach ($descendants as $descendant) {
            $excludeIds[] = $descendant->id;
        }
        
        $parentOptions = array_filter($parentOptions, function($key) use ($excludeIds) {
            return !in_array($key, $excludeIds);
        }, ARRAY_FILTER_USE_KEY);

        // Get category stats
        $stats = [
            'services_count' => $category->services()->count(),
            'children_count' => $category->children()->count(),
            'level' => $category->getLevel()
        ];

        return View::new('service_categories.form', compact('form', 'button', 'parentOptions', 'category', 'stats'));
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

        try {
            $category->save($fields);

            // Handle AJAX vs regular requests
            if ($response->isAjax()) {
                return $response->setData('category', $category->toArray())
                    ->setMessage('Service Category Updated')
                    ->setStatus(200);
            }

            return tr_redirect()->toPage('servicecategory', 'edit', $category->getID())
                ->withFlash('Service Category Updated', 'success');
                
        } catch (\Exception $e) {
            if ($response->isAjax()) {
                return $response->setError('update_failed', $e->getMessage())
                    ->setStatus(400);
            }

            return tr_redirect()->back()
                ->withFlash('Error updating category: ' . $e->getMessage(), 'error')
                ->withErrors($fields->getErrors());
        }
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
        // Load relationships for display
        $category->load(['parent', 'children.services', 'services']);
        
        // Get breadcrumb for navigation
        $breadcrumb = $category->getBreadcrumb();
        
        // Get statistics
        $stats = [
            'services_count' => $category->services()->count(),
            'children_count' => $category->children()->count(),
            'total_descendants' => $category->getDescendants()->count(),
            'level' => $category->getLevel(),
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at
        ];

        return View::new('service_categories.show', compact('category', 'breadcrumb', 'stats'));
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
        // Load relationships to show impact of deletion
        $category->load(['services', 'children']);
        
        $stats = [
            'services_count' => $category->services()->count(),
            'children_count' => $category->children()->count(),
            'can_delete' => !$category->hasServices() && !$category->hasChildren()
        ];

        return View::new('service_categories.delete', compact('category', 'stats'));
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

        try {
            $categoryName = $category->name;
            $category->delete();

            // Handle AJAX vs regular requests
            if ($response->isAjax()) {
                return $response->setMessage('Service Category "' . $categoryName . '" Deleted')
                    ->setStatus(200);
            }

            return tr_redirect()->toPage('servicecategory', 'index')
                ->withFlash('Service Category "' . $categoryName . '" Deleted', 'success');
                
        } catch (\Exception $e) {
            if ($response->isAjax()) {
                return $response->setError('deletion_failed', $e->getMessage())
                    ->setStatus(400);
            }

            return tr_redirect()->back()
                ->withFlash('Error deleting category: ' . $e->getMessage(), 'error');
        }
    }

    // ==========================================
    // API / AJAX HELPER METHODS
    // ==========================================

    /**
     * Get categories as JSON (for AJAX selects, etc.)
     */
    public function getJson(Response $response)
    {
        $categories = ServiceCategory::active()
            ->ordered()
            ->with('parent')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'full_name' => $category->getFullName(),
                    'parent_id' => $category->parent_id,
                    'level' => $category->getLevel(),
                    'has_children' => $category->hasChildren(),
                    'services_count' => $category->services()->count()
                ];
            });

        return $response->setData('categories', $categories);
    }

    /**
     * Get category tree as JSON (for hierarchical displays)
     */
    public function getTreeJson(Response $response)
    {
        // $tree = ServiceCategory::buildTree();
        
        return $response->setData('tree', $this->formatTreeForJson($tree));
    }

    /**
     * Reorder categories (for drag-drop sorting)
     */
    public function reorder(Response $response)
    {
        $orders = $response->getRequest()->getFields()['orders'] ?? [];
        
        if (!is_array($orders)) {
            return $response->setError('invalid_data', 'Invalid order data')
                ->setStatus(400);
        }

        try {
            foreach ($orders as $order) {
                if (isset($order['id']) && isset($order['sort_order'])) {
                    ServiceCategory::where('id', $order['id'])
                        ->update(['sort_order' => (int) $order['sort_order']]);
                }
            }

            return $response->setMessage('Categories reordered successfully');
            
        } catch (\Exception $e) {
            return $response->setError('reorder_failed', $e->getMessage())
                ->setStatus(500);
        }
    }

    // ==========================================
    // PRIVATE HELPER METHODS
    // ==========================================

    /**
     * Format tree data for JSON output
     */
    private function formatTreeForJson($categories)
    {
        $result = [];
        
        foreach ($categories as $category) {
            $item = [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'services_count' => $category->services()->count(),
                'children' => []
            ];

            if ($category->children->count() > 0) {
                $item['children'] = $this->formatTreeForJson($category->children);
            }

            $result[] = $item;
        }

        return $result;
    }
}