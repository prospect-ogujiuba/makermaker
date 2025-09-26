<?php

/**
 * ServiceEquipment Index View
 */

use MakerMaker\Models\ServiceEquipment;

$table = tr_table(ServiceEquipment::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'manufacturer' => [
        'label' => 'Manufacturer',
        'sort' => true
    ],
    'sku' => [
        'label' => 'SKU',
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
        'sort' => 'true'
    ]
], 'name')->setOrder('id', 'DESC')->render();
