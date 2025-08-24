<?php
namespace MakerMaker\Auth;

use MakerMaker\Models\User;
use MakerMaker\Models\ServiceCategory;
use TypeRocket\Models\AuthUser;
use TypeRocket\Auth\Policy;

/**
 * ServiceCategoryPolicy
 * 
 * Authorization policy for service category operations
 * Defines who can create, read, update, and delete service categories
 */
class ServiceCategoryPolicy extends Policy
{
    /**
     * Policy for reading/viewing service categories
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function read(AuthUser $auth, $category)
    {
        // Allow reading if user can manage services or view service categories
        return $auth->isCapable('manage_services') || 
               $auth->isCapable('read_service_categories') ||
               $auth->isCapable('view_service_categories');
    }

    /**
     * Policy for creating new service categories
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function create(AuthUser $auth, $category)
    {
        // Allow creation if user can manage services or create service categories
        return $auth->isCapable('manage_services') || 
               $auth->isCapable('create_service_categories');
    }

    /**
     * Policy for updating existing service categories
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function update(AuthUser $auth, $category)
    {
        // Allow update if user can manage services or edit service categories
        if ($auth->isCapable('manage_services') || 
            $auth->isCapable('edit_service_categories')) {
            return true;
        }

        // Additional check: if category has services, require higher permission
        if ($category instanceof ServiceCategory && $category->hasServices()) {
            return $auth->isCapable('manage_services');
        }

        return false;
    }

    /**
     * Policy for deleting service categories
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function destroy(AuthUser $auth, $category)
    {
        // Only allow deletion if user has full service management capabilities
        if (!$auth->isCapable('manage_services') && 
            !$auth->isCapable('delete_service_categories')) {
            return false;
        }

        // Additional safety checks if we have the actual category object
        if ($category instanceof ServiceCategory) {
            // Prevent deletion if category has services (business rule)
            if ($category->hasServices()) {
                return false;
            }

            // Prevent deletion if category has children (business rule)
            if ($category->hasChildren()) {
                return false;
            }

            // Extra protection for system/default categories
            // You can customize this based on your business needs
            if ($this->isSystemCategory($category)) {
                return $auth->isCapable('manage_system_data');
            }
        }

        return true;
    }

    /**
     * Policy for viewing category hierarchy/tree
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function viewTree(AuthUser $auth, $category)
    {
        return $this->read($auth, $category);
    }

    /**
     * Policy for reordering categories (drag & drop)
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function reorder(AuthUser $auth, $category)
    {
        return $auth->isCapable('manage_services') || 
               $auth->isCapable('edit_service_categories');
    }

    /**
     * Policy for bulk operations (bulk delete, bulk activate, etc.)
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function bulkOperations(AuthUser $auth, $category)
    {
        return $auth->isCapable('manage_services');
    }

    /**
     * Policy for importing/exporting categories
     * 
     * @param AuthUser $auth
     * @param ServiceCategory|object $category
     * @return bool
     */
    public function importExport(AuthUser $auth, $category)
    {
        return $auth->isCapable('manage_services') || 
               $auth->isCapable('import_export_data');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Check if a category is considered a "system" category
     * that requires special permissions to modify
     * 
     * @param ServiceCategory $category
     * @return bool
     */
    protected function isSystemCategory(ServiceCategory $category)
    {
        // Define system categories that should be protected
        $systemCategories = [
            'communications',
            'networking', 
            'security-systems',
            'managed-services',
            'installation-services'
        ];

        return in_array($category->slug, $systemCategories) || 
               $category->sort_order <= 5; // First 5 categories are "system"
    }

    /**
     * Check if user can modify category hierarchy
     * (moving categories between parents)
     * 
     * @param AuthUser $auth
     * @param ServiceCategory $category
     * @return bool
     */
    public function modifyHierarchy(AuthUser $auth, $category)
    {
        // Require full service management for hierarchy changes
        if (!$auth->isCapable('manage_services')) {
            return false;
        }

        // Extra protection for system categories
        if ($this->isSystemCategory($category)) {
            return $auth->isCapable('manage_system_data');
        }

        return true;
    }

    /**
     * Check if user can view category statistics
     * 
     * @param AuthUser $auth
     * @param ServiceCategory $category
     * @return bool
     */
    public function viewStatistics(AuthUser $auth, $category)
    {
        return $auth->isCapable('manage_services') || 
               $auth->isCapable('view_service_reports') ||
               $auth->isCapable('view_service_categories');
    }

    // ==========================================
    // ROLE-BASED HELPER METHODS
    // ==========================================

    /**
     * Quick check for admin users
     * 
     * @param AuthUser $auth
     * @return bool
     */
    protected function isAdmin(AuthUser $auth)
    {
        return $auth->hasRole('administrator') || 
               $auth->isCapable('manage_options');
    }

    /**
     * Quick check for service managers
     * 
     * @param AuthUser $auth
     * @return bool
     */
    protected function isServiceManager(AuthUser $auth)
    {
        return $auth->hasRole('service_manager') || 
               $auth->isCapable('manage_services');
    }

    /**
     * Quick check for editors who can modify content
     * 
     * @param AuthUser $auth
     * @return bool
     */
    protected function isEditor(AuthUser $auth)
    {
        return $auth->hasRole('editor') || 
               $auth->isCapable('edit_others_posts');
    }
}