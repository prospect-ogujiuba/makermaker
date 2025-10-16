<?php

/**
 * ComplexityLevel Index View
 */

use MakerMaker\Models\ComplexityLevel;

$table = tr_table(ComplexityLevel::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete']
    ],
    'level' => [
        'label' => 'Level',
        'sort' => 'true'
    ],
    'price_multiplier' => [
        'label' => 'Price Multiplier',
        'sort' => 'true'
    ],
    'created_at' => [
        'label' => 'Created At',
        'sort' => 'true'
    ],
    'updated_at' => [
        'label' => 'Updated At',
        'sort' => 'true'
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

$table;
