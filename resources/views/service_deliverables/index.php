<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\ServiceDeliverable::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Make Included' => 'make_included',
    'Make Additional' => 'make_additional',
    'Clear Additional Cost' => 'clear_cost',
    'Delete Selected' => 'delete_deliverables'
]);

$table->setSearchColumns([
    'deliverable_name' => 'Deliverable Name',
    'deliverable_description' => 'Description',
    'deliverable_type' => 'Type',
    'service_id' => 'Service ID',
    'quantity' => 'Quantity',
    'additional_cost' => 'Additional Cost',
    'is_included' => 'Included Status',
    'delivery_timeframe' => 'Timeframe',
    'sort_order' => 'Sort Order',
    'created_at' => 'Created Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('servicedeliverables'); ?>

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

        <!-- Deliverable Name Search -->
        <div class="tr-filter-group">
            <label>Deliverable Name:</label>
            <input type="text" name="deliverable_name" class="tr-filter"
                value="<?php echo $_GET['deliverable_name'] ?? ''; ?>"
                placeholder="Search Deliverable Name">
        </div>

        <!-- Deliverable Type Filter -->
        <div class="tr-filter-group">
            <label>Type:</label>
            <select name="deliverable_type" class="tr-filter">
                <option value="">All Types</option>
                <option value="equipment" <?= ($_GET['deliverable_type'] ?? '') === 'equipment' ? 'selected' : ''; ?>>Equipment</option>
                <option value="software" <?= ($_GET['deliverable_type'] ?? '') === 'software' ? 'selected' : ''; ?>>Software</option>
                <option value="documentation" <?= ($_GET['deliverable_type'] ?? '') === 'documentation' ? 'selected' : ''; ?>>Documentation</option>
                <option value="training" <?= ($_GET['deliverable_type'] ?? '') === 'training' ? 'selected' : ''; ?>>Training</option>
                <option value="access" <?= ($_GET['deliverable_type'] ?? '') === 'access' ? 'selected' : ''; ?>>Access/Credentials</option>
                <option value="support" <?= ($_GET['deliverable_type'] ?? '') === 'support' ? 'selected' : ''; ?>>Support</option>
            </select>
        </div>

        <!-- Included Status Filter -->
        <div class="tr-filter-group">
            <label>Status:</label>
            <select name="included_status" class="tr-filter">
                <option value="">All</option>
                <option value="included" <?= ($_GET['included_status'] ?? '') === 'included' ? 'selected' : ''; ?>>Included</option>
                <option value="additional" <?= ($_GET['included_status'] ?? '') === 'additional' ? 'selected' : ''; ?>>Additional Cost</option>
                <option value="optional" <?= ($_GET['included_status'] ?? '') === 'optional' ? 'selected' : ''; ?>>Optional (No Cost)</option>
            </select>
        </div>

        <!-- Cost Range Filters -->
        <div class="tr-filter-group">
            <label>Cost Min:</label>
            <input type="number" name="cost_min" class="tr-filter"
                value="<?php echo $_GET['cost_min'] ?? ''; ?>"
                placeholder="Min Cost" step="0.01" min="0">
        </div>

        <div class="tr-filter-group">
            <label>Cost Max:</label>
            <input type="number" name="cost_max" class="tr-filter"
                value="<?php echo $_GET['cost_max'] ?? ''; ?>"
                placeholder="Max Cost" step="0.01" min="0">
        </div>

        <!-- Quantity Range -->
        <div class="tr-filter-group">
            <label>Quantity Min:</label>
            <input type="number" name="quantity_min" class="tr-filter"
                value="<?php echo $_GET['quantity_min'] ?? ''; ?>"
                placeholder="Min Qty" min="1">
        </div>

        <div class="tr-filter-group">
            <label>Quantity Max:</label>
            <input type="number" name="quantity_max" class="tr-filter"
                value="<?php echo $_GET['quantity_max'] ?? ''; ?>"
                placeholder="Max Qty" min="1">
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

    // Deliverable name filter
    if (!empty($_GET['deliverable_name'])) {
        $model->where('deliverable_name', 'LIKE', '%' . $_GET['deliverable_name'] . '%');
    }

    // Deliverable type filter
    if (!empty($_GET['deliverable_type'])) {
        $model->where('deliverable_type', '=', $_GET['deliverable_type']);
    }

    // Included status filter
    if (!empty($_GET['included_status'])) {
        switch ($_GET['included_status']) {
            case 'included':
                $model->where('is_included', '=', 1);
                break;
            case 'additional':
                $model->where('is_included', '=', 0)->where('additional_cost', '>', 0);
                break;
            case 'optional':
                $model->where('is_included', '=', 0)->where('additional_cost', '=', 0);
                break;
        }
    }

    // Cost range filters
    if (!empty($_GET['cost_min'])) {
        $model->where('additional_cost', '>=', $_GET['cost_min']);
    }

    if (!empty($_GET['cost_max'])) {
        $model->where('additional_cost', '<=', $_GET['cost_max']);
    }

    // Quantity range filters
    if (!empty($_GET['quantity_min'])) {
        $model->where('quantity', '>=', $_GET['quantity_min']);
    }

    if (!empty($_GET['quantity_max'])) {
        $model->where('quantity', '<=', $_GET['quantity_max']);
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
    'deliverable_name' => [
        'label' => 'Deliverable',
        'sort' => true,
        'actions' => ['edit', 'delete'],
        'callback' => function($value, $item) {
            $output = '<strong>' . esc_html($item->deliverable_name) . '</strong>';
            if ($item->deliverable_description) {
                $output .= '<br><small class="text-muted">' . esc_html(wp_trim_words($item->deliverable_description, 12)) . '</small>';
            }
            return $output;
        }
    ],
    'service' => [
        'label' => 'Service',
        'sort' => false,
        'callback' => function($deliverable) {
            if ($deliverable->service) {
                return '<strong>' . esc_html($deliverable->service->name) . '</strong><br>' .
                       '<small class="text-muted">SKU: ' . esc_html($deliverable->service->sku) . '</small>';
            }
            return '<span class="text-muted">No Service</span>';
        }
    ],
    'deliverable_type' => [
        'label' => 'Type',
        'sort' => true,
        // 'callback' => function($deliverable) {
        //     $type_classes = [
        //         'equipment' => 'type-equipment',
        //         'software' => 'type-software',
        //         'documentation' => 'type-documentation',
        //         'training' => 'type-training',
        //         'access' => 'type-access',
        //         'support' => 'type-support'
        //     ];
        //     $class = $type_classes[$deliverable->deliverable_type] ?? 'type-default';
        //     return '<span class="badge ' . $class . '">' . esc_html($deliverable->getDeliverableTypeText()) . '</span>';
        // }
    ],
    'quantity_info' => [
        'label' => 'Quantity',
        'sort' => true,
        // 'callback' => function($deliverable) {
        //     return '<span class="quantity-display">' . esc_html($deliverable->getQuantityWithUnit()) . '</span>';
        // }
    ],
    'cost_info' => [
        'label' => 'Cost',
        'sort' => true,
        // 'callback' => function($deliverable) {
        //     $totalCost = $deliverable->getTotalCost();
            
        //     if ($deliverable->is_included) {
        //         return '<span class="badge badge-success">Included</span>';
        //     } elseif ($totalCost > 0) {
        //         $output = '<strong class="cost-amount">' . esc_html($deliverable->getFormattedTotalCost()) . '</strong>';
        //         if ($deliverable->quantity > 1) {
        //             $output .= '<br><small class="text-muted">' . esc_html($deliverable->getFormattedCost()) . ' each</small>';
        //         }
        //         return $output;
        //     } else {
        //         return '<span class="badge badge-info">Optional</span>';
        //     }
        // }
    ],
    'status_info' => [
        'label' => 'Status',
        'sort' => false,
        // 'callback' => function($deliverable) {
        //     if ($deliverable->is_included) {
        //         return '<span class="badge badge-success">Standard</span><br>' .
        //                '<small class="text-muted">Included in service</small>';
        //     } elseif ($deliverable->isPremium()) {
        //         return '<span class="badge badge-warning">Premium</span><br>' .
        //                '<small class="text-muted">Additional cost</small>';
        //     } else {
        //         return '<span class="badge badge-info">Optional</span><br>' .
        //                '<small class="text-muted">No additional cost</small>';
        //     }
        // }
    ],
    'delivery_info' => [
        'label' => 'Delivery',
        'sort' => false,
        // 'callback' => function($deliverable) {
        //     $timeframe = $deliverable->getDeliveryTimeframeText();
        //     return '<span class="delivery-timeframe">' . esc_html($timeframe) . '</span>';
        // }
    ],
    'sort_order' => [
        'label' => 'Order',
        'sort' => true,
        // 'callback' => function($deliverable) {
        //     return '<span class="sort-order">' . $deliverable->sort_order . '</span>';
        // }
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        // 'callback' => function($deliverable) {
        //     return $deliverable->created_at ? date('M j, Y', strtotime($deliverable->created_at)) : '';
        // }
    ]
]);


// Render the table
echo $table->render();