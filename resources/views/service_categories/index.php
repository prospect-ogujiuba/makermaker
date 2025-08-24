<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\ServiceCategory::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Categories' => 'activate_categories',
    'Deactivate Categories' => 'deactivate_categories',
    'Export Selected' => 'export_categories'
]);

$table->setSearchColumns([
    'name' => 'Category Name',
    'slug' => 'Category Slug',
    'description' => 'Description',
    'icon' => 'Icon',
    'sort_order' => 'Sort Order',
    'is_active' => 'Active Status',
    'created_at' => 'Created Date',
    'updated_at' => 'Updated Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('service_category'); ?>

    <div class="tr-search-filters">
        <!-- Basic Category Information -->
        <div class="tr-filter-group">
            <label>Category Name:</label>
            <input type="text" name="name" class="tr-filter"
                value="<?php echo $_GET['name'] ?? ''; ?>"
                placeholder="Search Category Name">
        </div>

        <div class="tr-filter-group">
            <label>Category Slug:</label>
            <input type="text" name="slug" class="tr-filter"
                value="<?php echo $_GET['slug'] ?? ''; ?>"
                placeholder="Search Category Slug">
        </div>

        <div class="tr-filter-group">
            <label>Description:</label>
            <input type="text" name="description" class="tr-filter"
                value="<?php echo $_GET['description'] ?? ''; ?>"
                placeholder="Search in description">
        </div>

        <!-- Hierarchy Filters -->
        <?php
        // Get all parent categories for filter dropdown
        $parentQuery = new Query();
        $parentCategories = $parentQuery
            ->table('wp_b2bcnc_service_categories')
            ->select('id', 'name')
            ->where('parent_id', 'IS', null)
            ->where('is_active', '=', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();
        ?>
        
        <?php if (!empty($parentCategories)): ?>
            <div class="tr-filter-group">
                <label>Parent Category:</label>
                <select name="parent_id" class="tr-filter">
                    <option value="">All Categories</option>
                    <option value="null" <?= ($_GET['parent_id'] ?? '') === 'null' ? 'selected' : ''; ?>>Top Level Only</option>
                    <?php foreach ($parentCategories as $parent): ?>
                        <option value="<?= htmlspecialchars($parent->id); ?>"
                            <?= ($_GET['parent_id'] ?? '') == $parent->id ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($parent->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- Status Filter -->
        <div class="tr-filter-group">
            <label>Active Status:</label>
            <select name="is_active" class="tr-filter">
                <option value="">All Categories</option>
                <option value="1" <?= ($_GET['is_active'] ?? '') === '1' ? 'selected' : ''; ?>>Active Only</option>
                <option value="0" <?= ($_GET['is_active'] ?? '') === '0' ? 'selected' : ''; ?>>Inactive Only</option>
            </select>
        </div>

        <!-- Icon Filter -->
        <div class="tr-filter-group">
            <label>Has Icon:</label>
            <select name="has_icon" class="tr-filter">
                <option value="">All Categories</option>
                <option value="1" <?= ($_GET['has_icon'] ?? '') === '1' ? 'selected' : ''; ?>>With Icon</option>
                <option value="0" <?= ($_GET['has_icon'] ?? '') === '0' ? 'selected' : ''; ?>>Without Icon</option>
            </select>
        </div>

        <!-- Sort Order Range -->
        <div class="tr-filter-group">
            <label>Sort Order Range:</label>
            <div class="tr-range-inputs">
                <input type="number" min="0" name="sort_min" class="tr-filter"
                    value="<?php echo $_GET['sort_min'] ?? ''; ?>"
                    placeholder="Min Order">
                <input type="number" min="0" name="sort_max" class="tr-filter"
                    value="<?php echo $_GET['sort_max'] ?? ''; ?>"
                    placeholder="Max Order">
            </div>
        </div>

        <!-- Date Filters -->
        <div class="tr-filter-group">
            <label>Created Date:</label>
            <div class="tr-date-inputs">
                <input type="date" name="created_from" class="tr-filter"
                    value="<?php echo $_GET['created_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="created_to" class="tr-filter"
                    value="<?php echo $_GET['created_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <div class="tr-filter-group">
            <label>Updated Date:</label>
            <div class="tr-date-inputs">
                <input type="date" name="updated_from" class="tr-filter"
                    value="<?php echo $_GET['updated_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="updated_to" class="tr-filter"
                    value="<?php echo $_GET['updated_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <!-- Category ID Filter -->
        <div class="tr-filter-group">
            <label>Category ID:</label>
            <input type="number" name="category_id" class="tr-filter"
                value="<?php echo $_GET['category_id'] ?? ''; ?>"
                placeholder="Enter Category ID">
        </div>

        <!-- Keywords Search -->
        <div class="tr-filter-group">
            <label>Keywords:</label>
            <input type="text" name="keywords" class="tr-filter"
                value="<?php echo $_GET['keywords'] ?? ''; ?>"
                placeholder="Search in name, slug, or description">
        </div>

    </div>
<?php
});

// Model filters
$table->addSearchModelFilter(function ($args, $model, $table) {
    // Basic Category Information Filters
    if (!empty($_GET['name'])) {
        $model->where('name', 'LIKE', '%' . $_GET['name'] . '%');
    }

    if (!empty($_GET['slug'])) {
        $model->where('slug', 'LIKE', '%' . $_GET['slug'] . '%');
    }

    if (!empty($_GET['description'])) {
        $model->where('description', 'LIKE', '%' . $_GET['description'] . '%');
    }

    // Parent Category Filter
    if (isset($_GET['parent_id']) && $_GET['parent_id'] !== '') {
        if ($_GET['parent_id'] === 'null') {
            $model->where('parent_id', 'IS', null);
        } else {
            $model->where('parent_id', '=', $_GET['parent_id']);
        }
    }

    // Status Filter
    if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
        $model->where('is_active', '=', $_GET['is_active']);
    }

    // Icon Filter
    if (isset($_GET['has_icon']) && $_GET['has_icon'] !== '') {
        if ($_GET['has_icon'] === '1') {
            $model->where('icon', 'IS NOT', null)->where('icon', '!=', '');
        } else {
            $model->where('icon', 'IS', null)->orWhere('icon', '=', '');
        }
    }

    // Sort Order Range
    if (!empty($_GET['sort_min'])) {
        $model->where('sort_order', '>=', $_GET['sort_min']);
    }

    if (!empty($_GET['sort_max'])) {
        $model->where('sort_order', '<=', $_GET['sort_max']);
    }

    // Date Filters
    if (!empty($_GET['created_from'])) {
        $model->where('created_at', '>=', $_GET['created_from'] . ' 00:00:00');
    }

    if (!empty($_GET['created_to'])) {
        $model->where('created_at', '<=', $_GET['created_to'] . ' 23:59:59');
    }

    if (!empty($_GET['updated_from'])) {
        $model->where('updated_at', '>=', $_GET['updated_from'] . ' 00:00:00');
    }

    if (!empty($_GET['updated_to'])) {
        $model->where('updated_at', '<=', $_GET['updated_to'] . ' 23:59:59');
    }

    // Category ID Filter
    if (!empty($_GET['category_id'])) {
        $model->where('id', '=', $_GET['category_id']);
    }

    // Keywords Search (searches across multiple fields)
    if (!empty($_GET['keywords'])) {
        $keywords = $_GET['keywords'];
        $model->where('name', 'LIKE', '%' . $keywords . '%')
            ->orWhere('slug', 'LIKE', '%' . $keywords . '%')
            ->orWhere('description', 'LIKE', '%' . $keywords . '%');
    }

    return $args;
});

// Configure the table with hierarchical category display
$table->setColumns([
    // Primary Category Information with hierarchy
    'name' => [
        'label' => 'Category Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
        'callback' => function ($value, $item) {
            $status_color = $item->is_active ? '#008000' : '#FF2C2C';
            $status_dot = '<span style="color:' . $status_color . '; font-size: 16px; margin-right: 5px;">●</span>';
            
            // Icon display
            $icon = '';
            if ($item->icon) {
                $icon = '<span class="dashicons ' . $item->icon . '" style="margin-right: 8px; color: #0073aa;"></span>';
            }
            
            return $status_dot . $icon . $value;
        }
    ],

    // Parent relationship
    'parent.name' => [
        'label' => 'Parent Category',
        'sort' => false,
        'callback' => function ($value, $item) {
            if ($item->parent_id && $value) {
                return '<span style="color: #666;">↳ ' . $value . '</span>';
            }
            return '<em style="color: #999;">Top Level</em>';
        }
    ],

    // Description with truncation
    'description' => [
        'label' => 'Description',
        'sort' => false,
        'callback' => function ($value, $item) {
            if (!$value) {
                return '<em style="color: #999;">No description</em>';
            }
            if (strlen($value) > 100) {
                return substr($value, 0, 100) . '<span style="color: #666;">...</span>';
            }
            return $value;
        }
    ],

    // Service count
    'services_count' => [
        'label' => 'Services',
        'sort' => false,
        'callback' => function ($value, $item) {
            $count = $item->services()->count() ?? 0;
            
            if ($count == 0) {
                return '<span style="color: #999;">0</span>';
            }
            
            $color = match (true) {
                $count >= 10 => '#008000',
                $count >= 5 => '#FFB800',
                default => '#0073aa'
            };
            
            return '<span style="color: ' . $color . '; font-weight: bold;">' . $count . '</span>';
        }
    ],

    // Sort order
    'sort_order' => [
        'label' => 'Order',
        'sort' => true,
        'callback' => function ($value, $item) {
            return '<span style="color: #666; font-family: monospace;">' . ($value ?? 0) . '</span>';
        }
    ],

    // Icon display
    'icon' => [
        'label' => 'Icon',
        'sort' => false,
        'callback' => function ($value, $item) {
            if (!$value) {
                return '<em style="color: #999;">None</em>';
            }
            return '<span class="dashicons ' . $value . '" style="font-size: 18px; color: #0073aa;" title="' . $value . '"></span>';
        }
    ],

    // Status
    'is_active' => [
        'label' => 'Status',
        'sort' => true,
        'callback' => function ($value, $item) {
            if ($value) {
                return '<span style="color: #008000; font-weight: bold;">Active</span>';
            } else {
                return '<span style="color: #FF2C2C; font-weight: bold;">Inactive</span>';
            }
        }
    ],

    // Timestamps
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        'callback' => function ($value, $item) {
            if ($value) {
                $date = new DateTime($value);
                $now = new DateTime();
                $diff = $now->diff($date);

                if ($diff->days == 0) {
                    return '<span style="color: #008000;">Today</span>';
                } elseif ($diff->days == 1) {
                    return '<span style="color: #FFB800;">Yesterday</span>';
                } elseif ($diff->days <= 7) {
                    return '<span style="color: #666;">' . $diff->days . ' days ago</span>';
                } else {
                    return $date->format('M j, Y');
                }
            }
            return '-';
        }
    ],

    'updated_at' => [
        'label' => 'Modified',
        'sort' => true,
        'callback' => function ($value, $item) {
            if ($value) {
                $date = new DateTime($value);
                $now = new DateTime();
                $diff = $now->diff($date);

                if ($diff->days == 0) {
                    if ($diff->h == 0) {
                        return '<span style="color: #008000;">' . $diff->i . 'm ago</span>';
                    }
                    return '<span style="color: #008000;">' . $diff->h . 'h ago</span>';
                } elseif ($diff->days <= 7) {
                    return '<span style="color: #666;">' . $diff->days . 'd ago</span>';
                } else {
                    return $date->format('M j');
                }
            }
            return '-';
        }
    ],

    'id' => [
        'label' => 'ID',
        'sort' => true
    ],
], 'id');

// Set default ordering to maintain hierarchy
// $table->setOrderBy('sort_order', 'ASC');
// $table->setSecondaryOrderBy('name', 'ASC');

// Custom CSS for hierarchy display
echo '<style>
.tr-table tbody tr td:first-child {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
}
</style>';

// Render the table
$table->render();