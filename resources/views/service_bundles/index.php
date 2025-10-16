<?php

/**
 * ServiceBundle Index View
 */

use MakerMaker\Models\ServiceBundle;

$table = tr_table(ServiceBundle::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Bundle Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'bundle_type' => [
        'label' => 'Type',
        'sort' => true,
        'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
    ],
    'total_discount_pct' => [
        'label' => 'Discount %',
        'sort' => true
    ],
    'short_desc' => [
        'label' => 'Short Description',
        'sort' => true
    ],
    'is_active' => [
        'label' => 'Active',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $item ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'valid_from' => [
        'label' => 'Valid From',
        'sort' => true
    ],
    'valid_to' => [
        'label' => 'Valid To',
        'sort' => true
    ],
    'updated_at' => [
        'label' => 'Updated At',
        'sort' => true
    ],
    'createdBy.user_nicename' => [
        'label' => 'Created By'
    ],
    'updatedBy.user_nicename' => [
        'label' => 'Updated By'
    ],
    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'name')->setOrder('id', 'DESC')->render();

$table;
