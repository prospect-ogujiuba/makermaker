<?php

/**
 * ServiceDeliverableAssignment Index View
 */

use MakerMaker\Models\ServiceDeliverableAssignment;

$table = tr_table(ServiceDeliverableAssignment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'deliverable.name' => [
        'label' => 'Deliverable Name',
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
