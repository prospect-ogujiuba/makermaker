<?php

use TypeRocket\Database\Query;

$table = tr_table(\MakerMaker\Models\ServiceComplexity::class);

$table->setBulkActions(tr_form()->useConfirm(), [
    'Activate Complexity Levels' => 'activate_complexity',
    'Deactivate Complexity Levels' => 'deactivate_complexity',
    'Delete Selected' => 'delete_complexity'
]);

$table->setSearchColumns([
    'name' => 'Complexity Name',
    'slug' => 'Slug',
    'description' => 'Description',
    'sort_order' => 'Sort Order',
    'is_active' => 'Active Status',
    'created_at' => 'Created Date',
]);

$table->addSearchFormFilter(function () {
    renderAdvancedSearchActions('servicecomplexity'); ?>

    <div class="tr-search-filters">
        <!-- Name Search -->
        <div class="tr-filter-group">
            <label>Complexity Name:</label>
            <input type="text" name="name" class="tr-filter"
                value="<?php echo $_GET['name'] ?? ''; ?>"
                placeholder="Search Complexity Name">
        </div>

        <!-- Slug Search -->
        <div class="tr-filter-group">
            <label>Slug:</label>
            <input type="text" name="slug" class="tr-filter"
                value="<?php echo $_GET['slug'] ?? ''; ?>"
                placeholder="Search Slug">
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

        <!-- Sort Order Range -->
        <div class="tr-filter-group">
            <label>Sort Order Min:</label>
            <input type="number" name="sort_min" class="tr-filter"
                value="<?php echo $_GET['sort_min'] ?? ''; ?>"
                placeholder="Min Order" min="0">
        </div>

        <div class="tr-filter-group">
            <label>Sort Order Max:</label>
            <input type="number" name="sort_max" class="tr-filter"
                value="<?php echo $_GET['sort_max'] ?? ''; ?>"
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
    // Name filter
    if (!empty($_GET['name'])) {
        $model->where('name', 'LIKE', '%' . $_GET['name'] . '%');
    }

    // Slug filter
    if (!empty($_GET['slug'])) {
        $model->where('slug', 'LIKE', '%' . $_GET['slug'] . '%');
    }

    // Active status filter
    if (isset($_GET['is_active']) && $_GET['is_active'] !== '') {
        $model->where('is_active', '=', $_GET['is_active']);
    }

    // Sort order range filters
    if (!empty($_GET['sort_min'])) {
        $model->where('sort_order', '>=', $_GET['sort_min']);
    }

    if (!empty($_GET['sort_max'])) {
        $model->where('sort_order', '<=', $_GET['sort_max']);
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
        'label' => 'Complexity Level',
        'sort' => true,
        'actions' => ['edit', 'delete'],


    ],
    'slug' => [
        'label' => 'Slug',
        'sort' => true,
    
    ],

    'sort_order' => [
        'label' => 'Order',
        'sort' => true,
      
    ],
    'is_active' => [
        'label' => 'Status',
        'sort' => true,
 
    ],
    'usage_info' => [
        'label' => 'Usage',
        'sort' => false,
        
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true,
        
    ]
]);

// Render the table
echo $table->render();
