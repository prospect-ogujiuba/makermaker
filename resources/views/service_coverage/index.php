<?php

/**
 * ServiceCoverage Index View
 */

use MakerMaker\Models\ServiceCoverage;

$table = tr_table(ServiceCoverage::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Primary Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'coverageArea.name' => [
        'label' => 'Addon Service Name',
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
        'label' => 'Addon Service Name',
        'sort' => true,
    ],
    'updatedBy.user_nicename' => [
        'label' => 'Addon Service Name',
        'sort' => true,
    ],

    'id' => [
        'label' => 'ID',
        'sort' => true
    ]
], 'service.name')->setOrder('id', 'DESC')->render();
