<?php

/**
 * ServiceEquipmentAssignment Index View
 */

use MakerMaker\Models\ServiceEquipmentAssignment;

$table = tr_table(ServiceEquipmentAssignment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service  Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'equipment.name' => [
        'label' => 'Equipment Name',
        'sort' => true,
    ],

    'required' => [
        'label' => 'Required',
        'sort' => true,
    ],

    'quantity' => [
        'label' => 'Quantity',
        'sort' => true,
    ],

    'substitute_ok' => [
        'label' => 'Subsitite Ok',
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
], 'service.name')->setOrder('id', 'DESC')->render();
