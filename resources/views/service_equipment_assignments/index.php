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
        'actions' => ['edit', 'view', 'delete']
    ],
    'equipment.name' => [
        'label' => 'Equipment Name',
        'sort' => true
    ],
    'required' => [
        'label' => 'Required',
        'sort' => true
    ],
    'quantity' => [
        'label' => 'Quantity',
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
], 'service.name')->setOrder('id', 'DESC')->render();
