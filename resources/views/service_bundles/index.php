<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\ServiceBundle::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Bundles' => 'activate_bundles',
    'Deactivate Bundles' => 'deactivate_bundles',
    'Delete Selected' => 'delete_bundles'
]);

$table->setSearchColumns([
    'name' => 'Bundle Name',
    'slug' => 'Slug',
    'bundle_type' => 'Bundle Type',
    'base_price' => 'Base Price',
    'discount_percentage' => 'Discount %',
    'is_active' => 'Active Status',
    'min_commitment_months' => 'Commitment',
    'created_at' => 'Created Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('servicebundles'); ?>

    <div class="tr-search-filters">
        <!-- Bundle Name Search -->
        <div class="tr-filter-group">
            <label>Bundle Name:</label>
            <input type="text" name="name" class="tr-filter"
                value="<?php echo $_GET['name'] ?? ''; ?>"
                placeholder="Search Bundle Name">
        </div>

        <!-- Bundle Type Filter -->
        <div class="tr-filter-group">
            <label>Bundle Type:</label>
            <select name="bundle_type" class="tr-filter">
                <option value="">All Types</option>
                <option value="package" <?= ($_GET['bundle_type'] ?? '') === 'package' ? 'selected' : ''; ?>>Service Package</option>
                <option value="addon_group" <?= ($_GET['bundle_type'] ?? '') === 'addon_group' ? 'selected' : ''; ?>>Addon Group</option>
                <option value="maintenance_plan" <?= ($_GET['bundle_type'] ?? '') === 'maintenance_plan' ? 'selected' : ''; ?>>Maintenance Plan</option>
                <option value="enterprise" <?= ($_GET['bundle_type'] ?? '') === 'enterprise' ? 'selected' : ''; ?>>Enterprise Solution</option>
            </select>
        </div>

        <!-- Price Range Filters -->
        <div class="tr-filter-group">
            <label>Price Min:</label>
            <input type="number" name="price_min" class="tr-filter"
                value="<?php echo $_GET['price_min'] ?? ''; ?>"
                placeholder="Min Price" step="0.01" min="0">
        </div>

        <div class="tr-filter-group">
            <label>Price Max:</label>
            <input type="number" name="price_max" class="tr-filter"
                value="<?php echo $_GET['price_max'] ?? ''; ?>"
                placeholder="Max Price" step="0.01" min="0">
        </div>

        <!-- Discount Range Filters -->
        <div class="tr-filter-group">
            <label>Discount Min %:</label>
            <input type="number" name="discount_min" class="tr-filter"
                value="<?php echo $_GET['discount_min'] ?? ''; ?>"
                placeholder="Min %" step="0.01" min="0" max="100">
        </div>

        <div class="tr-filter-group">
            <label>Discount Max %:</label>
            <input type="number" name="discount_max" class="tr-filter"
                value="<?php echo $_GET['discount_max'] ?? ''; ?>"
                placeholder="Max %" step="0.01" min="0" max="100">
        </div>

        <!-- Commitment Filter -->
        <div class="tr-filter-group">
            <label>Commitment:</label>
            <select name="commitment_filter" class="tr-filter">
                <option value="">All</option>
                <option value="none" <?= ($_GET['commitment_filter'] ?? '') === 'none' ? 'selected' : ''; ?>>No Commitment</option>
                <option value="has" <?= ($_GET['commitment_filter'] ?? '') === 'has' ? 'selected' : ''; ?>>Has Commitment</option>
            </select>
        </div>

        <!-- Active Status Filter -->
        <div class="tr-filter-group">
            <label>Status:</label>
            <select name="is_active" class="tr-filter">
                <option value="">All Statuses</option>
                <option value="1" <?= ($_GET['is_active'] ?? '') === '1' ? 'selected' : ''; ?>>Active</option>
                <option value="0" <?= ($_GET['is_active'] ?? '') === '0' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>

        <!-- Date Filters -->
        <div class="tr-filter-group">
            <label>Created From:</label>
            <input type="date" name="created_from" class="tr-filter"
                value="<?php echo $_GET['created_from'] ?? ''; ?>">
        </div>

        <div class="tr-filter-group">
            <label>Created To:</label>
            <input type="date" name="created_to" class="tr-filter"
                value="<?php echo $_GET['created_to'] ?? ''; ?>">
        </div>

    </div>
<?php
});

// Model filters
$table->addSearchModelFilter(function ($args, $model, $table) {
    // Bundle name filter
    if (!empty($_GET['name'])) {
        $model->where('name', 'LIKE', '%' . $_GET['name'] . '%');
    }

    // Bundle type filter
    if (!empty($_GET['bundle_type'])) {
        $model->where('bundle_type', '=', $_GET['bundle_type']);
    }

    // Price range filters
    if (!empty($_GET['price_min'])) {
        $model->where('base_price', '>=', $_GET['price_min']);
    }

    if (!empty($_GET['price_max'])) {
        $model->where('base_price', '<=', $_GET['price_max']);
    }

    // Discount range filters
    if (!empty($_GET['discount_min'])) {
        $model->where('discount_percentage', '>=', $_GET['discount_min']);
    }

    if (!empty($_GET['discount_max'])) {
        $model->where('discount_percentage', '<=', $_GET['discount_max']);
    }

    // Commitment filter
    if (!empty($_GET['commitment_filter'])) {
        if ($_GET['commitment_filter'] === 'none') {
            $model->where('min_commitment_months', '<=', 0)->orWhereNull('min_commitment_months');
        } elseif ($_GET['commitment_filter'] === 'has') {
            $model->where('min_commitment_months', '>', 0);
        }
    }

    // Active status filter
    if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
        $model->where('is_active', '=', $_GET['is_active']);
    }

    // Date filters
    if (!empty($_GET['created_from'])) {
        $model->where('created_at', '>=', $_GET['created_from'] . ' 00:00:00');
    }

    if (!empty($_GET['created_to'])) {
        $model->where('created_at', '<=', $_GET['created_to'] . ' 23:59:59');
    }
});

// Custom columns
$table->setColumns([
    'name' => [
        'label' => 'Bundle Name',
        'sort' => true,
        'actions' => ['edit', 'delete'],
        'callback' => function($bundle) {
            $output = '<strong>' . esc_html($bundle->name) . '</strong><br>';
            $output .= '<small class="text-muted">Slug: ' . esc_html($bundle->slug) . '</small>';
            if ($bundle->description) {
                $output .= '<br><small class="bundle-description">' . esc_html(wp_trim_words($bundle->description, 12)) . '</small>';
            }
            return $output;
        }
    ],
    'bundle_type' => [
        'label' => 'Type',
        'sort' => true,
        'callback' => function($bundle) {
            $type_classes = [
                'package' => 'bundle-type-package',
                'addon_group' => 'bundle-type-addon',
                'maintenance_plan' => 'bundle-type-maintenance',
                'enterprise' => 'bundle-type-enterprise'
            ];
            $class = $type_classes[$bundle->bundle_type] ?? 'bundle-type-default';
            return '<span class="badge ' . $class . '">' . esc_html($bundle->getBundleTypeText()) . '</span>';
        }
    ],
    'services_info' => [
        'label' => 'Services',
        'sort' => false,
        'callback' => function($bundle) {
            $totalServices = $bundle->getServicesCount();
            $activeServices = $bundle->getActiveServicesCount();
            
            if ($totalServices > 0) {
                $output = '<span class="services-count">' . $totalServices . ' service' . ($totalServices !== 1 ? 's' : '') . '</span>';
                if ($activeServices !== $totalServices) {
                    $output .= '<br><small class="text-muted">' . $activeServices . ' active</small>';
                }
                return $output;
            }
            return '<span class="text-muted">No services</span>';
        }
    ],
    'pricing_info' => [
        'label' => 'Pricing',
        'sort' => true,
        'callback' => function($bundle) {
            $output = '<div class="pricing-info">';
            
            if ($bundle->base_price) {
                $output .= '<strong>' . esc_html($bundle->getFormattedPrice()) . '</strong>';
                
                if ($bundle->discount_percentage > 0) {
                    $output .= '<br><small class="discount-info">-' . number_format($bundle->discount_percentage, 1) . '%</small>';
                }
                
                // Show actual discount if we can calculate it
                $actualDiscount = $bundle->getActualDiscountPercentage();
                if ($actualDiscount > 0 && $actualDiscount !== $bundle->discount_percentage) {
                    $output .= '<br><small class="text-success">Actual: -' . number_format($actualDiscount, 1) . '%</small>';
                }
            } else {
                $output .= '<span class="text-muted">Custom pricing</span>';
            }
            
            $output .= '</div>';
            return $output;
        }
    ],
    'commitment' => [
        'label' => 'Commitment',
        'sort' => true,
        'callback' => function($bundle) {
            return '<span class="commitment-info">' . esc_html($bundle->getCommitmentText()) . '</span>';
        }
    ],
    'is_active' => [
        'label' => 'Status',
        'sort' => true,
        'callback' => function($bundle) {
            if ($bundle->is_active) {
                if ($bundle->isAvailable()) {
                    return '<span class="badge badge-success">Active</span>';
                } else {
                    return '<span class="badge badge-warning" title="Active but no active services">Limited</span>';
                }
            }
            return '<span class="badge badge-secondary">Inactive</span>';
        }
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        'callback' => function($bundle) {
            return $bundle->created_at ? date('M j, Y', strtotime($bundle->created_at)) : '';
        }
    ]
]);



// Render the table
echo $table->render();