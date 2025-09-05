<?php
/**
 * ServiceEquipment Index View
 */

use MakerMaker\Models\ServiceEquipment;

$table = tr_table(ServiceEquipment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'manufacturer' => [
        'label' => 'Manufacturer',
        'sort' => true,
    ],

    'sku' => [
        'label' => 'SKU',
        'sort' => true,
    ],

    'specs' => [
        'label' => 'Specs',
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
