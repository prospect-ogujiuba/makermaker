<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\Service::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Services' => 'activate_services',
    'Deactivate Services' => 'deactivate_services',
    'Update Pricing' => 'update_pricing',
    'Export Selected' => 'export_services'
]);

$table->setSearchColumns([
    'name' => 'Service Name',
    'code' => 'Service Code',
    'description' => 'Category',
    'base_price' => 'Base Price',
    'active' => 'Active Status',
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
            <label>Service Code:</label>
            <input type="text" name="code" class="tr-filter"
                value="<?php echo $_GET['code'] ?? ''; ?>"
                placeholder="Search Service Code">
        </div>

        <div class="tr-filter-group">
            <label>Category/Description:</label>
            <input type="text" name="description" class="tr-filter"
                value="<?php echo $_GET['description'] ?? ''; ?>"
                placeholder="Search Category">
        </div>

        <!-- Status Filter -->
        <div class="tr-filter-group">
            <label>Active Status:</label>
            <select name="active" class="tr-filter">
                <option value="">All Services</option>
                <option value="1" <?= ($_GET['active'] ?? '') === '1' ? 'selected' : ''; ?>>Active Only</option>
                <option value="0" <?= ($_GET['active'] ?? '') === '0' ? 'selected' : ''; ?>>Inactive Only</option>
            </select>
        </div>

        <!-- Pricing Filters -->
        <div class="tr-filter-group">
            <label>Price Range:</label>
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
            <label>Pricing Type:</label>
            <select name="pricing_type" class="tr-filter">
                <option value="">All Types</option>
                <option value="has_price" <?= ($_GET['pricing_type'] ?? '') === 'has_price' ? 'selected' : ''; ?>>Fixed Price</option>
                <option value="quote_required" <?= ($_GET['pricing_type'] ?? '') === 'quote_required' ? 'selected' : ''; ?>>Quote Required</option>
            </select>
        </div>

        <!-- Service Categories (if you have predefined categories) -->
        <?php
        // Get unique categories from existing services
        $categoryQuery = new Query();
        $categories = $categoryQuery
            ->table('wp_b2bcnc_services')
            ->select('name')
            ->distinct()
            ->where('name', '!=', '')
            ->where('name', '!=', null)
            ->orderBy('name', 'ASC')
            ->get();
        ?>
        <?php if (!empty($categories)): ?>
        <div class="tr-filter-group">
            <label>Category:</label>
            <select name="category_filter" class="tr-filter">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category->name); ?>"
                        <?= ($_GET['category_filter'] ?? '') === $category->name ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category->name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>

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
                placeholder="Search in name, code, or description">
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

    if (!empty($_GET['code'])) {
        $model->where('code', 'LIKE', '%' . $_GET['code'] . '%');
    }

    if (!empty($_GET['description'])) {
        $model->where('description', 'LIKE', '%' . $_GET['description'] . '%');
    }

    // Status Filter
    if (isset($_GET['active']) && $_GET['active'] !== '') {
        $model->where('active', '=', $_GET['active']);
    }

    // Pricing Filters
    if (!empty($_GET['price_min'])) {
        $model->where('base_price', '>=', $_GET['price_min']);
    }

    if (!empty($_GET['price_max'])) {
        $model->where('base_price', '<=', $_GET['price_max']);
    }

    if (!empty($_GET['pricing_type'])) {
        if ($_GET['pricing_type'] === 'has_price') {
            $model->where('base_price', '>', 0)->where('base_price', '!=', null);
        } elseif ($_GET['pricing_type'] === 'quote_required') {
            $model->where(function($query) {
                $query->where('base_price', '=', 0)
                      ->orWhere('base_price', '=', null);
            });
        }
    }

    // Category Filter
    if (!empty($_GET['category_filter'])) {
        $model->where('name', '=', $_GET['category_filter']);
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
        $model->where(function($query) use ($keywords) {
            $query->where('name', 'LIKE', '%' . $keywords . '%')
                  ->orWhere('code', 'LIKE', '%' . $keywords . '%')
                  ->orWhere('description', 'LIKE', '%' . $keywords . '%');
        });
    }

    return $args;
});

$table->setColumns([
    'name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'code' => [
        'label' => 'Service Code',
        'sort' => true,
    ],
    'description' => [
        'label' => 'Category',
        'sort' => true,
    ],
    'base_price' => [
        'label' => 'Base Price',
        'sort' => true,
        'callback' => function ($value) {
            return $value ? '$' . number_format($value, 2) : 'Quote Required';
        }
    ],
    'active' => [
        'label' => 'Active',
        'sort' => true,
        'callback' => function ($value) {
            return $value ? '<span style="color:#008000;">✓</span>' : '<span style="color:#FF2C2C;">✗</span>';
        }
    ],
    'id' => [
        'label' => 'ID',
        'sort' => true,
    ],
], 'id');

$table->render();