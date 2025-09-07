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

    'short_desc' => [
        'label' => 'Description',
        'sort' => false,
        'callback' => function ($value, $item) {
            return $value ? substr($value, 0, 80) . (strlen($value) > 80 ? '...' : '') : '-';
        }
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

    'is_active' => [
        'label' => 'Active',
        'callback' => function ($value, $item) {
            return $value ? '✓' : '✗';
        }
    ],

    'default_unit' => [
        'label' => 'Unit',
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

    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'name')->setOrder('id', 'DESC')->render();