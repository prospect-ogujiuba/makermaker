<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\ServiceAddon::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Addons' => 'activate_addons',
    'Deactivate Addons' => 'deactivate_addons',
    'Delete Selected' => 'delete_addons'
]);

$table->setSearchColumns([
    'addon_name' => 'Addon Name',
    'service_id' => 'Service ID',
    'addon_type' => 'Addon Type',
    'price' => 'Price',
    'is_recurring' => 'Recurring',
    'billing_frequency' => 'Billing Frequency',
    'is_active' => 'Active Status',
    'sort_order' => 'Sort Order',
    'created_at' => 'Created Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('serviceaddons'); ?>

    <div class="tr-search-filters">
        <!-- Service Filter -->
        <div class="tr-filter-group">
            <label>Service:</label>
            <select name="service_id" class="tr-filter">
                <option value="">All Services</option>
                <?php
                $services = \MakerMaker\Models\Service::new()->get();
                foreach ($services as $service) :
                    $selected = ($_GET['service_id'] ?? '') == $service->id ? 'selected' : '';
                ?>
                    <option value="<?= $service->id; ?>" <?= $selected; ?>>
                        <?= esc_html($service->name); ?> (<?= esc_html($service->sku); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Addon Name Search -->
        <div class="tr-filter-group">
            <label>Addon Name:</label>
            <input type="text" name="addon_name" class="tr-filter"
                value="<?php echo $_GET['addon_name'] ?? ''; ?>"
                placeholder="Search Addon Name">
        </div>

        <!-- Addon Type Filter -->
        <div class="tr-filter-group">
            <label>Addon Type:</label>
            <select name="addon_type" class="tr-filter">
                <option value="">All Types</option>
                <option value="upgrade" <?= ($_GET['addon_type'] ?? '') === 'upgrade' ? 'selected' : ''; ?>>Upgrade</option>
                <option value="additional" <?= ($_GET['addon_type'] ?? '') === 'additional' ? 'selected' : ''; ?>>Additional Feature</option>
                <option value="extended_warranty" <?= ($_GET['addon_type'] ?? '') === 'extended_warranty' ? 'selected' : ''; ?>>Extended Warranty</option>
                <option value="training" <?= ($_GET['addon_type'] ?? '') === 'training' ? 'selected' : ''; ?>>Training</option>
                <option value="support" <?= ($_GET['addon_type'] ?? '') === 'support' ? 'selected' : ''; ?>>Support</option>
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

        <!-- Recurring Filter -->
        <div class="tr-filter-group">
            <label>Recurring:</label>
            <select name="is_recurring" class="tr-filter">
                <option value="">All</option>
                <option value="1" <?= ($_GET['is_recurring'] ?? '') === '1' ? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?= ($_GET['is_recurring'] ?? '') === '0' ? 'selected' : ''; ?>>No</option>
            </select>
        </div>

        <!-- Billing Frequency Filter -->
        <div class="tr-filter-group">
            <label>Billing Frequency:</label>
            <select name="billing_frequency" class="tr-filter">
                <option value="">All Frequencies</option>
                <option value="monthly" <?= ($_GET['billing_frequency'] ?? '') === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                <option value="quarterly" <?= ($_GET['billing_frequency'] ?? '') === 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                <option value="annually" <?= ($_GET['billing_frequency'] ?? '') === 'annually' ? 'selected' : ''; ?>>Annually</option>
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
    // Service filter
    if (!empty($_GET['service_id'])) {
        $model->where('service_id', '=', $_GET['service_id']);
    }

    // Addon name filter
    if (!empty($_GET['addon_name'])) {
        $model->where('addon_name', 'LIKE', '%' . $_GET['addon_name'] . '%');
    }

    // Addon type filter
    if (!empty($_GET['addon_type'])) {
        $model->where('addon_type', '=', $_GET['addon_type']);
    }

    // Price range filters
    if (!empty($_GET['price_min'])) {
        $model->where('price', '>=', $_GET['price_min']);
    }

    if (!empty($_GET['price_max'])) {
        $model->where('price', '<=', $_GET['price_max']);
    }

    // Recurring filter
    if (isset($_GET['is_recurring']) && $_GET['is_recurring'] !== '') {
        $model->where('is_recurring', '=', $_GET['is_recurring']);
    }

    // Billing frequency filter
    if (!empty($_GET['billing_frequency'])) {
        $model->where('billing_frequency', '=', $_GET['billing_frequency']);
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
    'addon_name' => [
        'label' => 'Addon Name',
        'sort' => true,
        'actions' => ['edit', 'delete'],
        'callback' => function ($value, $item) {
            $featured = $item->is_featured ? ' <span style="color:#FFB800; font-size: 14px;">★</span>' : '';
            $status_color = match ($item->is_active ?? 'active') {
                '1' => '#008000',
                '0' => '#FF2C2C',
            };
            $status_dot = '<span style="color:' . $status_color . '; font-size: 16px; margin-right: 5px;">●</span>';
            return $status_dot . $value . $featured;
        }
    ],
    'service.name' => [
        'label' => 'Service',
        'sort' => true,

    ],
        'services_count' => [
        'label' => 'Services',
        'sort' => false,
        'callback' => function ($value, $item) {
            $count = $item->service()->count() ?? 0;
            
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
    'addon_type' => [
        'label' => 'Type',
        'sort' => true,

    ],
    'price' => [
        'label' => 'Price',
        'sort' => true,

    ],
    'is_recurring' => [
        'label' => 'Recurring',
        'sort' => true,

    ],
    'is_active' => [
        'label' => 'Status',
        'sort' => true,

    ]
]);

// Render the table
echo $table->render();
