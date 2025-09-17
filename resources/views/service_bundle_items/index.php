<?php

/**
 * ServiceBundleItem Index View
 */

use MakerMaker\Models\ServiceBundleItem;

$table = tr_table(ServiceBundleItem::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'bundle.name' => [
        'label' => 'Bundle Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'service.name' => [
        'label' => 'Service Name',
        'sort' => true,
    ],

    'quantity' => [
        'label' => 'Quantity',
        'sort' => true,
    ],

    'discount_pct' => [
        'label' => 'Discount %',
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
        'sort' => true,
    ],
    'updatedBy.user_nicename' => [
        'label' => ''Updated By',
        'sort' => true,
    ],

    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'bundle.name')->setOrder('id', 'DESC')->render();
