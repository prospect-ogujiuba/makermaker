<?php

/**
 * Service Index View
 */

use MakerMaker\Models\Service;

$table = tr_table(Service::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'sku' => [
        'label' => 'SKU',
        'sort' => true,
    ],

    'slug' => [
        'label' => 'Slug',
        'sort' => true,
    ],

    'category_id' => [
        'label' => 'Category',
        'callback' => function ($value, $item) {
            // Assuming you have a relationship to categories
            return $item->category ? $item->category->name : 'N/A';
        }
    ],

    'service_type_id' => [
        'label' => 'Type',
        'callback' => function ($value, $item) {
            // Assuming you have a relationship to service types
            return $item->serviceType ? $item->serviceType->name : 'N/A';
        }
    ],

    'complexity_id' => [
        'label' => 'Complexity',
        'callback' => function ($value, $item) {
            // Assuming you have a relationship to complexities
            return $item->complexity ? $item->complexity->name : 'N/A';
        }
    ],

    'default_unit' => [
        'label' => 'Unit',
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
], 'name')->setOrder('id', 'DESC')->render();