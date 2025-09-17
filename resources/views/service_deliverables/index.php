<?php

/**
 * ServiceDeliverable Index View
 */

use MakerMaker\Models\ServiceDeliverable;

$table = tr_table(ServiceDeliverable::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'description' => [
        'label' => 'Description',
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
        'label' => 'Updated By',
    ],
    'id' => [
        'label' => 'ID',
        'sort' => 'true'
    ]
], 'name')->setOrder('ID', 'DESC')->render();
