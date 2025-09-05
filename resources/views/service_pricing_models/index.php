<?php

/**
 * ServicePricingModel Index View
 */

use MakerMaker\Models\ServicePricingModel;

$table = tr_table(ServicePricingModel::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('servicepricingmodel'); ?>

    <?php
    // Service Prices
    $servicePrices = tr_query()
        ->table(GLOBAL_WPDB_PREFIX . 'srvc_service_prices')
        ->select('id', 'pricing_model_id', 'service_id')
        ->orderBy('id', 'ASC')
        ->get();

    // Users
    $users = tr_query()
        ->table(GLOBAL_WPDB_PREFIX . 'users')
        ->select('id', 'user_nicename', 'user_email')
        ->orderBy('user_nicename', 'ASC')
        ->get();
    ?>

    <div class="tr-search-filters">
        <!-- Name Search -->
        <div class="tr-filter-group">
            <label>Name:</label>
            <input type="text" name="name" class="tr-filter"
                value="<?php echo $_GET['name'] ?? ''; ?>"
                placeholder="Search Name">
        </div>

        <!-- Code Search -->
        <div class="tr-filter-group">
            <label>Code:</label>
            <input type="text" name="code" class="tr-filter"
                value="<?php echo $_GET['code'] ?? ''; ?>"
                placeholder="Search Code">
        </div>

        <!-- ID Search -->
        <div class="tr-filter-group">
            <label>ID:</label>
            <input type="number" name="id" class="tr-filter"
                value="<?php echo $_GET['id'] ?? ''; ?>"
                placeholder="Search ID">
        </div>

        <!-- Created By Search -->
        <div class="tr-filter-group">
            <label>Created By:</label>
            <select name="created_by" class="tr-filter">
                <option value="">Select User</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= htmlspecialchars($user->id); ?>"
                        <?= ($_GET['created_by'] ?? '') === $user->id ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($user->user_nicename); ?> -
                        (<?= htmlspecialchars($user->user_email); ?>)
                    </option>
                <?php endforeach; ?>
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

        <div class="tr-filter-group">
            <label>Updated From:</label>
            <input type="date" name="updated_from" class="tr-filter"
                value="<?php echo $_GET['updated_from'] ?? ''; ?>">
        </div>

        <div class="tr-filter-group">
            <label>Updated To:</label>
            <input type="date" name="updated_to" class="tr-filter"
                value="<?php echo $_GET['updated_to'] ?? ''; ?>">
        </div>

    </div>

<?php
});

// Add filter handling
$table->addSearchModelFilter(function ($args, $model, $query) {

    if (!empty($_GET['name'])) {
        $model->where('name', 'LIKE', '%' . $_GET['name'] . '%');
    }

    if (!empty($_GET['code'])) {
        $model->where('code', 'LIKE', '%' . $_GET['code'] . '%');
    }

    if (!empty($_GET['id'])) {
        $model->where('id', '=', $_GET['id']);
    }

    if (!empty($_GET['created_by'])) {
        $model->where('created_by', '=', $_GET['created_by']);
    }

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

    return $args;
});

// Configure the table with service pricing model data
$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'code' => [
        'label' => 'Code',
        'sort' => true,
    ],

    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        'callback' => function ($value, $item) {
            return date('M j, Y g:i A', strtotime($value));
        }
    ],

    'updated_at' => [
        'label' => 'Updated',
        'sort' => true,
        'callback' => function ($value, $item) {
            return date('M j, Y g:i A', strtotime($value));
        }
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
