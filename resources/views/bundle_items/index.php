<?php

/**
 * BundleItem Index View
 */

use MakerMaker\Models\BundleItem;

$table = tr_table(BundleItem::class);

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
        'sort' => true,
        'callback' => function($value) {
            return number_format($value, 3);
        }
    ],
    'discount_pct' => [
        'label' => 'Discount %',
        'sort' => true,
        'callback' => function($value) {
            return number_format($value, 2) . '%';
        }
    ],
    'is_optional' => [
        'label' => 'Optional',
        'sort' => true,
        'callback' => function($value) {
            return $value ? '<span style="color: #0073aa;">✓ Optional</span>' : '<span style="color: #666;">Required</span>';
        }
    ],
    'sort_order' => [
        'label' => 'Order',
        'sort' => true
    ],
    'created_at' => [
        'label' => 'Created',
        'sort' => true
    ],
    'createdBy.user_nicename' => [
        'label' => 'Created By'
    ],
    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'bundle.name')->setOrder('sort_order', 'ASC')->render();
