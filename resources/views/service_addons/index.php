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
        'actions' => ['edit', 'view', 'delete'],
    ],

    'addonService.name' => [
        'label' => 'Addon Service Name',
        'sort' => true,
    ],

    'required' => [
        'label' => 'Required',
        'sort' => true,
    ],

    'min_qty' => [
        'label' => 'Min Qty',
        'sort' => true,
    ],

    'max_qty' => [
        'label' => 'Max Qty',
        'sort' => true,
    ],

    'price_delta' => [
        'label' => 'Price Delta',
        'sort' => true,
    ],

    'multiplier' => [
        'label' => 'Multiplier',
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
], 'service.name')->setOrder('id', 'DESC')->render();
