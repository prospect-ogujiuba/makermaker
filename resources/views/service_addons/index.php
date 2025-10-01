<?php

/**
 * ServiceAddon Index View
 */

use MakerMaker\Models\ServiceAddon;

$table = tr_table(ServiceAddon::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Primary Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],

    'addonService.name' => [
        'label' => 'Addon Service Name',
        'sort' => true
    ],

    'required' => [
        'label' => 'Required',
        'sort' => true,
          'callback' => function ($value) {
            return $value ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],

    'min_qty' => [
        'label' => 'Min Qty',
        'sort' => true
    ],

    'max_qty' => [
        'label' => 'Max Qty',
        'sort' => true
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
        'sort' => true
    ]
], 'service.name')->setOrder('id', 'DESC')->render();
