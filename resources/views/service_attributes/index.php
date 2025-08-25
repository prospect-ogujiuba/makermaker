<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\ServiceAttribute::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Make Configurable' => 'make_configurable',
    'Make Display Only' => 'make_display_only',
    'Delete Selected' => 'delete_attributes'
]);

$table->setSearchColumns([
    'attribute_name' => 'Attribute Name',
    'attribute_value' => 'Attribute Value',
    'attribute_type' => 'Attribute Type',
    'service_id' => 'Service ID',
    'is_configurable' => 'Configurable',
    'display_order' => 'Display Order',
    'created_at' => 'Created Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('serviceattributes'); ?>

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

        <!-- Attribute Name Search -->
        <div class="tr-filter-group">
            <label>Attribute Name:</label>
            <input type="text" name="attribute_name" class="tr-filter"
                value="<?php echo $_GET['attribute_name'] ?? ''; ?>"
                placeholder="Search Attribute Name">
        </div>

        <!-- Attribute Type Filter -->
        <div class="tr-filter-group">
            <label>Attribute Type:</label>
            <select name="attribute_type" class="tr-filter">
                <option value="">All Types</option>
                <option value="text" <?= ($_GET['attribute_type'] ?? '') === 'text' ? 'selected' : ''; ?>>Text</option>
                <option value="number" <?= ($_GET['attribute_type'] ?? '') === 'number' ? 'selected' : ''; ?>>Number</option>
                <option value="boolean" <?= ($_GET['attribute_type'] ?? '') === 'boolean' ? 'selected' : ''; ?>>Boolean</option>
                <option value="json" <?= ($_GET['attribute_type'] ?? '') === 'json' ? 'selected' : ''; ?>>JSON</option>
                <option value="url" <?= ($_GET['attribute_type'] ?? '') === 'url' ? 'selected' : ''; ?>>URL</option>
                <option value="email" <?= ($_GET['attribute_type'] ?? '') === 'email' ? 'selected' : ''; ?>>Email</option>
            </select>
        </div>

        <!-- Attribute Value Search -->
        <div class="tr-filter-group">
            <label>Attribute Value:</label>
            <input type="text" name="attribute_value" class="tr-filter"
                value="<?php echo $_GET['attribute_value'] ?? ''; ?>"
                placeholder="Search Attribute Value">
        </div>

        <!-- Configurable Filter -->
        <div class="tr-filter-group">
            <label>Configurable:</label>
            <select name="is_configurable" class="tr-filter">
                <option value="">All</option>
                <option value="1" <?= ($_GET['is_configurable'] ?? '') === '1' ? 'selected' : ''; ?>>Yes</option>
                <option value="0" <?= ($_GET['is_configurable'] ?? '') === '0' ? 'selected' : ''; ?>>No</option>
            </select>
        </div>

        <!-- Display Order Range -->
        <div class="tr-filter-group">
            <label>Display Order Min:</label>
            <input type="number" name="order_min" class="tr-filter"
                value="<?php echo $_GET['order_min'] ?? ''; ?>"
                placeholder="Min Order" min="0">
        </div>

        <div class="tr-filter-group">
            <label>Display Order Max:</label>
            <input type="number" name="order_max" class="tr-filter"
                value="<?php echo $_GET['order_max'] ?? ''; ?>"
                placeholder="Max Order" min="0">
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

    // Attribute name filter
    if (!empty($_GET['attribute_name'])) {
        $model->where('attribute_name', 'LIKE', '%' . $_GET['attribute_name'] . '%');
    }

    // Attribute type filter
    if (!empty($_GET['attribute_type'])) {
        $model->where('attribute_type', '=', $_GET['attribute_type']);
    }

    // Attribute value filter
    if (!empty($_GET['attribute_value'])) {
        $model->where('attribute_value', 'LIKE', '%' . $_GET['attribute_value'] . '%');
    }

    // Configurable filter
    if (isset($_GET['is_configurable']) && $_GET['is_configurable'] !== '') {
        $model->where('is_configurable', '=', $_GET['is_configurable']);
    }

    // Display order range filters
    if (!empty($_GET['order_min'])) {
        $model->where('display_order', '>=', $_GET['order_min']);
    }

    if (!empty($_GET['order_max'])) {
        $model->where('display_order', '<=', $_GET['order_max']);
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
    'attribute_name' => [
        'label' => 'Attribute',
        'sort' => true,
        'actions' => ['edit', 'delete'],
        
    ],
    'service' => [
        'label' => 'Service',
        'sort' => false,
        'callback' => function($attribute) {
            if ($attribute->service) {
                return '<strong>' . esc_html($attribute->service->name) . '</strong><br>' .
                       '<small class="text-muted">SKU: ' . esc_html($attribute->service->sku) . '</small>';
            }
            return '<span class="text-muted">No Service</span>';
        }
    ],
    'attribute_value' => [
        'label' => 'Value',
        'sort' => false,
        'callback' => function($attribute) {
            if (empty($attribute->attribute_value)) {
                return '<span class="text-muted">Empty</span>';
            }

            $value = $attribute->attribute_value;
            $displayValue = '';
            
            switch ($attribute->attribute_type) {
                case 'boolean':
                    $boolValue = $attribute->getBooleanValue();
                    $displayValue = '<span class="boolean-value ' . ($boolValue ? 'bool-true' : 'bool-false') . '">' .
                                  ($boolValue ? 'Yes' : 'No') . '</span>';
                    break;
                    
                case 'number':
                    $numValue = $attribute->getNumericValue();
                    $displayValue = '<span class="number-value">' . number_format($numValue, 2) . '</span>';
                    break;
                    
                case 'url':
                    $displayValue = '<a href="' . esc_url($value) . '" target="_blank" rel="noopener" class="url-value">' . 
                                  esc_html(wp_trim_words($value, 6)) . ' <span class="dashicons dashicons-external"></span></a>';
                    break;
                    
                case 'email':
                    $displayValue = '<a href="mailto:' . esc_attr($value) . '" class="email-value">' . 
                                  esc_html($value) . '</a>';
                    break;
                    
                case 'json':
                    $jsonData = $attribute->getJsonValue();
                    if ($jsonData && is_array($jsonData)) {
                        $displayValue = '<span class="json-value" title="' . esc_attr(json_encode($jsonData)) . '">' .
                                      'JSON (' . count($jsonData) . ' items)</span>';
                    } else {
                        $displayValue = '<span class="json-value">JSON Data</span>';
                    }
                    break;
                    
                case 'text':
                default:
                    $displayValue = '<span class="text-value">' . esc_html(wp_trim_words($value, 8)) . '</span>';
                    break;
            }
            
            return $displayValue;
        }
    ],
    'attribute_type' => [
        'label' => 'Type',
        'sort' => true,
        
    ],
    'configurable_status' => [
        'label' => 'Configurable',
        'sort' => true,
        
    ],
    'display_order' => [
        'label' => 'Order',
        'sort' => true,
  
    ],
    'validity_status' => [
        'label' => 'Status',
        'sort' => false,
       
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
       
    ]
]);

// Render the table
echo $table->render();