<?php

/**
 * Equipment Index View
 */

use MakerMaker\Models\Equipment;

$table = tr_table(Equipment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'manufacturer' => [
        'label' => 'Manufacturer',
        'sort' => true
    ],
    'model' => [
        'label' => 'Model',
        'sort' => true
    ],
    'category' => [
        'label' => 'Category',
        'sort' => true,
    ],
    'sku' => [
        'label' => 'SKU',
        'sort' => true,
        'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
    ],
    'unit_cost' => [
        'label' => 'Unit Cost',
        'sort' => true
    ],
    'is_consumable' => [
        'label' => 'Consumable',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $item ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true
    ],
    'updated_at' => [
        'label' => 'Updated',
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
