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
        'actions' => ['edit', 'view', 'delete']
    ],
    'service.name' => [
        'label' => 'Service Name',
        'sort' => true
    ],
    'quantity' => [
        'label' => 'Quantity',
        'sort' => true
    ],
    'discount_pct' => [
        'label' => 'Discount %',
        'sort' => true
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
], 'bundle.name')->setOrder('id', 'DESC')->render();
