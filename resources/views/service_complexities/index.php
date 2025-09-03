<?php

/**
 * ServiceComplexity Index View
 * 
 * This view displays a list of serviceComplexities.
 * Add your index/list functionality here.
 */

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceComplexity;
use TypeRocket\Database\Query;
use TypeRocket\Register\Page;

$table = tr_table(\MakerMaker\Models\ServiceComplexity::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('servicecomplexity'); ?>

    <?php
    // Services

    $services = tr_query()
        ->table('wp_srvc_services')
        ->select('id', 'name', 'sku', 'short_desc')
        ->orderBy('name', 'ASC')
        ->get();

    // Users
    $users = tr_query()
        ->table('wp_users')
        ->select('id', 'user_nicename', 'user_email')
        ->orderBy('user_nicename', 'ASC')
        ->get();
    ?>

    <div class="tr-search-filters">
        <!-- Service Search -->
        <div class="tr-filter-group">
            <label>Complexity Service:</label>
            <select name="service" class="tr-filter">
                <option value="">Select Service</option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= htmlspecialchars($service->id); ?>"
                        <?= ($_GET['service'] ?? '') === $service->id ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($service->name); ?> -
                        (<?= htmlspecialchars($service->sku); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Name Search -->
        <div class="tr-filter-group">
            <label>Name:</label>
            <input type="text" name="name" class="tr-filter"
                value="<?php echo $_GET['name'] ?? ''; ?>"
                placeholder="Search Name">
        </div>

        <!-- ID Search -->
        <div class="tr-filter-group">
            <label>ID:</label>
            <input type="number" name="id" class="tr-filter"
                value="<?php echo $_GET['id'] ?? ''; ?>"
                placeholder="Search ID">
        </div>

        <!-- Level Search -->
        <div class="tr-filter-group">
            <label>Level:</label>
            <input type="number" name="level" class="tr-filter"
                value="<?php echo $_GET['level'] ?? ''; ?>"
                placeholder="Search Level">
        </div>

        <!-- Price Multiplier Search - FIXED: removed space from name attribute -->
        <div class="tr-filter-group">
            <label>Price Multiplier:</label>
            <input type="number" step="0.01" name="price_multiplier" class="tr-filter"
                value="<?php echo $_GET['price_multiplier'] ?? ''; ?>"
                placeholder="Search Price Multiplier">
        </div>

        <!-- System Information -->

        <div class="tr-filter-group">
            <label>Created Date:</label>
            <div class="tr-date-inputs">
                <input type="date" name="created_date_from" class="tr-filter"
                    value="<?php echo $_GET['created_date_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="created_date_to" class="tr-filter"
                    value="<?php echo $_GET['created_date_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <div class="tr-filter-group">
            <label>Updated Date:</label>
            <div class="tr-date-inputs">
                <input type="date" name="updated_date_from" class="tr-filter"
                    value="<?php echo $_GET['updated_date_from'] ?? ''; ?>"
                    placeholder="From">
                <input type="date" name="updated_date_to" class="tr-filter"
                    value="<?php echo $_GET['updated_date_to'] ?? ''; ?>"
                    placeholder="To">
            </div>
        </div>

        <!-- User Search -->
        <div class="tr-filter-group">
            <label>Created By User:</label>
            <select name="created_by" class="tr-filter">
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user->id); ?>"
                        <?= ($_GET['created_by'] ?? '') === $user->id ? 'selected' : ''; ?>>
                        <?= ucwords(htmlspecialchars($user->user_nicename), '\-\_'); ?> -
                        (<?= htmlspecialchars($user->user_email); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- User Search -->
        <div class="tr-filter-group">
            <label>Updated By User:</label>
            <select name="updated_by" class="tr-filter">
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user->id); ?>"
                        <?= ($_GET['updated_by'] ?? '') === $user->id ? 'selected' : ''; ?>>
                        <?= ucwords(htmlspecialchars($user->user_nicename), '\-\_'); ?> -
                        (<?= htmlspecialchars($user->user_email); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>
<?php
});

// Model filters - ALIGNED to match the form filters above
$table->addSearchModelFilter(function ($args, $model, $table) {
    // Service filter - NEW: added to match form
    if (!empty($_GET['service'])) {
        // Assuming there's a relationship to services table
        // $model->where('service_id', '=', $_GET['service']);

        $model->join('wp_srvc_services', 'wp_srvc_services.complexity_id', '=', 'wp_srvc_complexities.id')
            ->where('wp_srvc_services.id', '=', $_GET['service']);
    }

    // Name filter
    if (!empty($_GET['name'])) {
        $model->where('name', 'LIKE', '%' . $_GET['name'] . '%');
    }

    // ID filter - NEW: added to match form
    if (!empty($_GET['id'])) {
        $model->where('id', '=', $_GET['id']);
    }

    // Level filter - NEW: added to match form
    if (!empty($_GET['level'])) {
        $model->where('level', '=', $_GET['level']);
    }

    // Price Multiplier filter
    if (!empty($_GET['price_multiplier'])) {
        $model->where('price_multiplier', 'LIKE', '%' . $_GET['price_multiplier'] . '%');
    }

    // Date filters - FIXED: aligned with form field names
    if (!empty($_GET['created_date_from'])) {
        $model->where('created_at', '>=', $_GET['created_date_from'] . ' 00:00:00');
    }

    if (!empty($_GET['created_date_to'])) {
        $model->where('created_at', '<=', $_GET['created_date_to'] . ' 23:59:59');
    }

    if (!empty($_GET['updated_date_from'])) {
        $model->where('updated_at', '>=', $_GET['updated_date_from'] . ' 00:00:00');
    }

    if (!empty($_GET['updated_date_to'])) {
        $model->where('updated_at', '<=', $_GET['updated_date_to'] . ' 23:59:59');
    }

    // User filters
    if (!empty($_GET['created_by'])) {
        $model->where('created_by', '=', $_GET['created_by']);
    }

    if (!empty($_GET['updated_by'])) {
        $model->where('updated_by', '=', $_GET['updated_by']);
    }

    return $args;
});


$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete'],

    ],
    'level' => [
        'label' => 'Level',
        'sort' => 'true'
    ],
    'price_multiplier' => [
        'label' => 'Price Multiplier',
        'sort' => 'true'
    ],
    'created_at' => [
        'label' => 'Created At',
        'sort' => 'true'
    ],
    'updated_at' => [
        'label' => 'Updated At',
        'sort' => 'true'
    ],
    'createdBy.user_nicename' => [
        'label' => 'Created By',
    ],
    'updatedBy.user_nicename' => [
        'label' => 'Last Updated By',
    ],
    'id' => [
        'label' => 'ID',
        'sort' => 'true'
    ]
], 'name')->setOrder('ID', 'DESC')->render();

$table;
