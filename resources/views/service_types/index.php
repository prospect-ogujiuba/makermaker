<?php

/**
 * ServiceType Index View
 */

use MakerMaker\Models\ServiceType;

$table = tr_table(ServiceType::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],

    'code' => [
        'label' => 'Code',
        'sort' => true
    ],

    'services' => [
        'label' => 'Services',
        'callback' => function ($value, $item) {
            return count((array)$item->services);
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
        'sort' => 'true'
    ]
], 'name')->setOrder('ID', 'DESC')->render();
