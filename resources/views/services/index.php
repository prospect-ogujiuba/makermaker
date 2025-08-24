<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\Service::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Services' => 'activate_services',
    'Deactivate Services' => 'deactivate_services',
    'Mark as Featured' => 'mark_featured',
    'Remove Featured' => 'remove_featured',
    'Export Selected' => 'export_services'
]);

$table->setSearchColumns([
    'name' => 'Service Name',
    'slug' => 'Service Slug',
    'short_description' => 'Short Description',
    'service_type' => 'Service Type',
    'delivery_method' => 'Delivery Method',
    'pricing_model' => 'Pricing Model',
    'base_price' => 'Base Price',
    'hourly_rate' => 'Hourly Rate',
    'complexity_level' => 'Complexity',
    'is_active' => 'Active Status',
    'is_featured' => 'Featured Status',
    'created_at' => 'Created Date',
    'updated_at' => 'Updated Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('service'); ?>

    <div class="tr-search-filters">
        <!-- Basic Service Information -->
        <div class="tr-filter-group">
            <label>Service Name:</label>
            <input type="text" name="name" class="tr-filter"
                value="<?php echo $_GET['name'] ?? ''; ?>"
                placeholder="Search Service Name">
        </div>

        <div class="tr-filter-group">
            <label>Service Slug:</label>
            <input type="text" name="slug" class="tr-filter"
                value="<?php echo $_GET['slug'] ?? ''; ?>"
                placeholder="Search Service Slug">
        </div>

        <div class="tr-filter-group">
            <label>Service SKU:</label>
            <input type="text" name="sku" class="tr-filter"
                value="<?php echo $_GET['sku'] ?? ''; ?>"
                placeholder="Search Service SKU">
        </div>

        <div class="tr-filter-group">
            <label>Description:</label>
            <input type="text" name="description" class="tr-filter"
                value="<?php echo $_GET['description'] ?? ''; ?>"
                placeholder="Search Description">
        </div>

        <!-- Service Type Filter -->
        <div class="tr-filter-group">
            <label>Service Type:</label>
            <select name="service_type" class="tr-filter">
                <option value="">All Types</option>
                <option value="installation" <?= ($_GET['service_type'] ?? '') === 'installation' ? 'selected' : ''; ?>>Installation</option>
                <option value="maintenance" <?= ($_GET['service_type'] ?? '') === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                <option value="hosting" <?= ($_GET['service_type'] ?? '') === 'hosting' ? 'selected' : ''; ?>>Hosting</option>
                <option value="consulting" <?= ($_GET['service_type'] ?? '') === 'consulting' ? 'selected' : ''; ?>>Consulting</option>
                <option value="support" <?= ($_GET['service_type'] ?? '') === 'support' ? 'selected' : ''; ?>>Support</option>
                <option value="hybrid" <?= ($_GET['service_type'] ?? '') === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
            </select>
        </div>

        <!-- Delivery Method Filter -->
        <div class="tr-filter-group">
            <label>Delivery Method:</label>
            <select name="delivery_method" class="tr-filter">
                <option value="">All Methods</option>
                <option value="onsite" <?= ($_GET['delivery_method'] ?? '') === 'onsite' ? 'selected' : ''; ?>>On-site</option>
                <option value="remote" <?= ($_GET['delivery_method'] ?? '') === 'remote' ? 'selected' : ''; ?>>Remote</option>
                <option value="hybrid" <?= ($_GET['delivery_method'] ?? '') === 'hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                <option value="hosted" <?= ($_GET['delivery_method'] ?? '') === 'hosted' ? 'selected' : ''; ?>>Hosted</option>
            </select>
        </div>

        <!-- Pricing Model Filter -->
        <div class="tr-filter-group">
            <label>Pricing Model:</label>
            <select name="pricing_model" class="tr-filter">
                <option value="">All Models</option>
                <option value="fixed" <?= ($_GET['pricing_model'] ?? '') === 'fixed' ? 'selected' : ''; ?>>Fixed Price</option>
                <option value="hourly" <?= ($_GET['pricing_model'] ?? '') === 'hourly' ? 'selected' : ''; ?>>Hourly Rate</option>
                <option value="monthly" <?= ($_GET['pricing_model'] ?? '') === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                <option value="project" <?= ($_GET['pricing_model'] ?? '') === 'project' ? 'selected' : ''; ?>>Project-based</option>
                <option value="tiered" <?= ($_GET['pricing_model'] ?? '') === 'tiered' ? 'selected' : ''; ?>>Tiered</option>
                <option value="custom" <?= ($_GET['pricing_model'] ?? '') === 'custom' ? 'selected' : ''; ?>>Custom Quote</option>
            </select>
        </div>

        <!-- Complexity Filter -->
        <div class="tr-filter-group">
            <label>Complexity Level:</label>
            <select name="complexity_level" class="tr-filter">
                <option value="">All Levels</option>
                <option value="basic" <?= ($_GET['complexity_level'] ?? '') === 'basic' ? 'selected' : ''; ?>>Basic</option>
                <option value="intermediate" <?= ($_GET['complexity_level'] ?? '') === 'intermediate' ? 'selected' : ''; ?>>Intermediate</option>
                <option value="advanced" <?= ($_GET['complexity_level'] ?? '') === 'advanced' ? 'selected' : ''; ?>>Advanced</option>
                <option value="expert" <?= ($_GET['complexity_level'] ?? '') === 'expert' ? 'selected' : ''; ?>>Expert</option>
            </select>
        </div>

        <!-- Status Filters -->
        <div class="tr-filter-group">
            <label>Active Status:</label>
            <select name="is_active" class="tr-filter">
                <option value="">All Services</option>
                <option value="1" <?= ($_GET['is_active'] ?? '') === '1' ? 'selected' : ''; ?>>Active Only</option>
                <option value="0" <?= ($_GET['is_active'] ?? '') === '0' ? 'selected' : ''; ?>>Inactive Only</option>
            </select>
        </div>

        <div class="tr-filter-group">
            <label>Featured Status:</label>
            <select name="is_featured" class="tr-filter">
                <option value="">All Services</option>
                <option value="1" <?= ($_GET['is_featured'] ?? '') === '1' ? 'selected' : ''; ?>>Featured Only</option>
                <option value="0" <?= ($_GET['is_featured'] ?? '') === '0' ? 'selected' : ''; ?>>Not Featured</option>
            </select>
        </div>

        <!-- Pricing Filters -->
        <div class="tr-filter-group">
            <label>Base Price Range:</label>
            <div class="tr-range-inputs">
                <input type="number" min="0" step="0.01" name="price_min" class="tr-filter"
                    value="<?php echo $_GET['price_min'] ?? ''; ?>"
                    placeholder="Min Price">
                <input type="number" min="0" step="0.01" name="price_max" class="tr-filter"
                    value="<?php echo $_GET['price_max'] ?? ''; ?>"
                    placeholder="Max Price">
            </div>
        </div>

        <div class="tr-filter-group">
            <label>Hourly Rate Range:</label>
            <div class="tr-range-inputs">
                <input type="number" min="0" step="0.01" name="hourly_min" class="tr-filter"
                    value="<?php echo $_GET['hourly_min'] ?? ''; ?>"
                    placeholder="Min Rate">
                <input type="number" min="0" step="0.01" name="hourly_max" class="tr-filter"
                    value="<?php echo $_GET['hourly_max'] ?? ''; ?>"
                    placeholder="Max Rate">
            </div>
        </div>

        <!-- Service Categories -->
        <?php
        // Get unique categories from existing services
        $categoryQuery = new Query();
        $categories = $categoryQuery
            ->table('wp_b2bcnc_service_categories')
            ->select('id', 'name')
            ->orderBy('name', 'ASC')
            ->get();
        ?>
        <?php if (!empty($categories)): ?>
            <div class="tr-filter-group">
                <label>Category:</label>
                <select name="category_id" class="tr-filter">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category->id); ?>"
                            <?= ($_GET['category_id'] ?? '') == $category->id ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- Requirements Filters -->
        <div class="tr-filter-group">
            <label>Site Visit Required:</label>
            <select name="requires_site_visit" class="tr-filter">
                <option value="">All Services</option>
                <option value="1" <?= ($_GET['requires_site_visit'] ?? '') === '1' ? 'selected' : ''; ?>>Required</option>
                <option value="0" <?= ($_GET['requires_site_visit'] ?? '') === '0' ? 'selected' : ''; ?>>Not Required</option>
            </select>
        </div>

        <div class="tr-filter-group">
            <label>Remote Delivery:</label>
            <select name="supports_remote_delivery" class="tr-filter">
                <option value="">All Services</option>
                <option value="1" <?= ($_GET['supports_remote_delivery'] ?? '') === '1' ? 'selected' : ''; ?>>Supported</option>
                <option value="0" <?= ($_GET['supports_remote_delivery'] ?? '') === '0' ? 'selected' : ''; ?>>Not Supported</option>
            </select>
        </div>

        <div class="tr-filter-group">
            <label>Assessment Required:</label>
            <select name="requires_assessment" class="tr-filter">
                <option value="">All Services</option>
                <option value="1" <?= ($_GET['requires_assessment'] ?? '') === '1' ? 'selected' : ''; ?>>Required</option>
                <option value="0" <?= ($_GET['requires_assessment'] ?? '') === '0' ? 'selected' : ''; ?>>Not Required</option>
            </select>
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

        <!-- Service ID Filter -->
        <div class="tr-filter-group">
            <label>Service ID:</label>
            <input type="number" name="service_id" class="tr-filter"
                value="<?php echo $_GET['service_id'] ?? ''; ?>"
                placeholder="Enter Service ID">
        </div>

        <!-- Advanced Text Search -->
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
    // Basic Service Information Filters
    if (!empty($_GET['name'])) {
        $model->where('name', 'LIKE', '%' . $_GET['name'] . '%');
    }

    if (!empty($_GET['slug'])) {
        $model->where('slug', 'LIKE', '%' . $_GET['slug'] . '%');
    }

    if (!empty($_GET['sku'])) {
        $model->where('sku', 'LIKE', '%' . $_GET['sku'] . '%');
    }

    if (!empty($_GET['description'])) {
        $model->where('short_description', 'LIKE', '%' . $_GET['description'] . '%')->orWhere('long_description', 'LIKE', '%' . $_GET['description'] . '%');
    }

    // Service Type Filter
    if (!empty($_GET['service_type'])) {
        $model->where('service_type', '=', $_GET['service_type']);
    }

    // Delivery Method Filter
    if (!empty($_GET['delivery_method'])) {
        $model->where('delivery_method', '=', $_GET['delivery_method']);
    }

    // Pricing Model Filter
    if (!empty($_GET['pricing_model'])) {
        $model->where('pricing_model', '=', $_GET['pricing_model']);
    }

    // Complexity Level Filter
    if (!empty($_GET['complexity_level'])) {
        $model->where('complexity_level', '=', $_GET['complexity_level']);
    }

    // Status Filters
    if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
        $model->where('is_active', '=', $_GET['is_active']);
    }

    if (isset($_GET['is_featured']) && $_GET['is_featured'] !== '') {
        $model->where('is_featured', '=', $_GET['is_featured']);
    }

    // Pricing Filters
    if (!empty($_GET['price_min'])) {
        $model->where('base_price', '>=', $_GET['price_min']);
    }

    if (!empty($_GET['price_max'])) {
        $model->where('base_price', '<=', $_GET['price_max']);
    }

    if (!empty($_GET['hourly_min'])) {
        $model->where('hourly_rate', '>=', $_GET['hourly_min']);
    }

    if (!empty($_GET['hourly_max'])) {
        $model->where('hourly_rate', '<=', $_GET['hourly_max']);
    }

    // Category Filter
    if (!empty($_GET['category_id'])) {
        $model->where('category_id', '=', $_GET['category_id']);
    }

    // Requirements Filters
    if (isset($_GET['requires_site_visit']) && $_GET['requires_site_visit'] !== '') {
        $model->where('requires_site_visit', '=', $_GET['requires_site_visit']);
    }

    if (isset($_GET['supports_remote_delivery']) && $_GET['supports_remote_delivery'] !== '') {
        $model->where('supports_remote_delivery', '=', $_GET['supports_remote_delivery']);
    }

    if (isset($_GET['requires_assessment']) && $_GET['requires_assessment'] !== '') {
        $model->where('requires_assessment', '=', $_GET['requires_assessment']);
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

    // Service ID Filter
    if (!empty($_GET['service_id'])) {
        $model->where('id', '=', $_GET['service_id']);
    }

    // Keywords Search (searches across multiple fields)
    if (!empty($_GET['keywords'])) {
        $keywords = $_GET['keywords'];
        $model->where('name', 'LIKE', '%' . $keywords . '%')
            ->orWhere('slug', 'LIKE', '%' . $keywords . '%')
            ->orWhere('short_description', 'LIKE', '%' . $keywords . '%')
            ->orWhere('long_description', 'LIKE', '%' . $keywords . '%');;
    }

    return $args;
});



// Configure the table with comprehensive service data
$table->setColumns([
    // Primary Service Information
    'name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
        'callback' => function ($value, $item) {
            $featured = $item->is_featured ? ' <span style="color:#FFB800; font-size: 14px;">★</span>' : '';
            $status_color = match ($item->status ?? 'active') {
                'active' => '#008000',
                'inactive' => '#FF2C2C',
                'draft' => '#FFB800',
                'archived' => '#666666',
                default => '#008000'
            };
            $status_dot = '<span style="color:' . $status_color . '; font-size: 16px; margin-right: 5px;">●</span>';
            return $status_dot . $value . $featured;
        }
    ],

    // Service Classification
    'service.service_category.name' => [
        'label' => 'Category',
        'sort' => true,
        'callback' => function ($value, $item) {
            $category = $item->serviceCategory ?? null;
            if ($category && $category->icon) {
                return '<span class="dashicons ' . $category->icon . '" style="margin-right: 5px;"></span>' . $value;
            }
            return $value ?: '<em>Uncategorized</em>';
        }
    ],

    'service_type' => [
        'label' => 'Type',
        'sort' => true,
        'callback' => function ($value, $item) {

            return ucfirst($value) ?: '-';
        }
    ],

    'complexity_level' => [
        'label' => 'Complexity',
        'sort' => true,
        'callback' => function ($value, $item) {

            return ucfirst($value) ?: 'Basic';
        }
    ],

    'base_price' => [
        'label' => 'Base Price',
        'sort' => true,
        'callback' => function ($value, $item) {
            if (!$value || $value == 0) {
                return '<em style="color: #666;">Quote Required</em>';
            }

            $formatted = '$' . number_format($value, 2);

            // Add unit type if available
            if ($item->unit_type) {
                $formatted .= ' <small style="color: #666;">per ' . $item->unit_type . '</small>';
            }

            // Add minimum billable indicator
            if ($item->min_billable && $item->min_billable > 0) {
                $formatted .= '<br><small style="color: #0073aa;">Min: $' . number_format($item->min_billable, 2) . '</small>';
            }

            return $formatted;
        }
    ],

    'pricing_model' => [
        'label' => 'Pricing Model',
        'sort' => true,
        'callback' => function ($value, $item) {
            return ucfirst($value) ?: 'Not Set';
        }
    ],

    // Delivery & Operations
    'delivery_method' => [
        'label' => 'Delivery',
        'sort' => true,
        'callback' => function ($value, $item) {
            $method = $item->serviceDeliveryMethod ?? null;
            $icons = [];

            if ($item->requires_site_visit) $icons[] = '🏢';
            if ($item->supports_remote_delivery) $icons[] = '💻';
            if ($item->requires_assessment) $icons[] = '📝';
            if ($method && $method->requires_location) $icons[] = '📍';

            $icon_str = !empty($icons) ? implode(' ', $icons) . ' ' : '';
            return $icon_str . (ucfirst($value) ?: 'Not Set');
        }
    ],

    'min_notice_days' => [
        'label' => 'Lead Time',
        'sort' => true,
        'callback' => function ($value, $item) {
            if (!$value || $value == 0) {
                return '<span style="color: #008000;">Same Day</span>';
            }

            $urgency_color = match (true) {
                $value <= 1 => '#008000',
                $value <= 7 => '#FFB800',
                $value <= 30 => '#FF8C00',
                default => '#FF2C2C'
            };

            $unit = $value == 1 ? 'day' : 'days';
            return '<span style="color: ' . $urgency_color . ';">' . $value . ' ' . $unit . '</span>';
        }
    ],


    // System Information
    'sku' => [
        'label' => 'SKU',
        'sort' => true,
        'callback' => function ($value, $item) {
            return $value ?: '<em style="color: #666;">Auto</em>';
        }
    ],

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

// Render the table
$table->render();
