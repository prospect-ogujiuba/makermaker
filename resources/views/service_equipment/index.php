<?php

/**
 * ServiceEquipment Index View
 */

use MakerMaker\Models\ServiceEquipment;

$table = tr_table(ServiceEquipment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'equipment.name' => [
        'label' => 'Equipment Name',
        'sort' => true
    ],
    'quantity' => [
        'label' => 'Quantity',
        'sort' => true
    ],
    'quantity_unit' => [
        'label' => 'Unit',
        'sort' => true
    ],
    'required' => [
        'label' => 'Required',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $item ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'cost_included' => [
        'label' => 'Cost Included',
        'sort' => true,
        'callback' => function ($item, $value) {
            return $item ? "<i class='bi bi-check' style='color: green;'></i>" : "<i class='bi bi-x' style='color: red;'></i>";
        }
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
