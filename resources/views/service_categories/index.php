<?php

/**
 * ServiceCategory Index View
 */

use MakerMaker\Models\ServiceCategory;

$table = tr_table(ServiceCategory::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete']
    ],
    'parentCategory.name' => [
        'label' => 'Parent',
        'sort' => 'true',
        'callback' => function ($value, $item) {
            return $value ?? '<span class="text-muted">Top Level</span>';
        }
    ],
    'slug' => [
        'label' => 'Slug',
        'sort' => 'true',
        'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
    ],
    'sort_order' => [
        'label' => 'Order',
        'sort' => 'true',
        'callback' => function ($value) {
            return '<span class="badge badge-secondary">' . $value . '</span>';
        }
    ],
    'is_active' => [
        'label' => 'Status',
        'sort' => 'true',
        'callback' => function ($value) {
            return $value ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'description' => [
        'label' => 'Description',
        'callback' => function ($value) {
            return $value ?
                (strlen($value) > 50 ? substr($value, 0, 47) . '...' : $value) :
                '<span class="text-muted">No description</span>';
        }
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
        'label' => 'Created By'
    ],
    'updatedBy.user_nicename' => [
        'label' => 'Updated By'
    ],
    'id' => [
        'label' => 'ID',
        'sort' => 'true'
    ]
], 'sort_order')->setOrder('id', 'DESC')->render();

$table;
