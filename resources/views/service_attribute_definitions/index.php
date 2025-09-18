<?php

/**
 * ServiceAttributeDefinition Index View
 */

use MakerMaker\Models\ServiceAttributeDefinition;

$table = tr_table(ServiceAttributeDefinition::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'label' => [
        'label' => 'Label',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete']
    ],
    'service_type_id' => [
        'label' => 'Parent ID',
        'sort' => 'true'
    ],
    'code' => [
        'label' => 'Code',
        'sort' => 'true'
    ],
    'data_type' => [
        'label' => 'Data Type',
        'sort' => 'true'
    ],
    'unit' => [
        'label' => 'Unit',
        'sort' => 'true'
    ],
    'required' => [
        'label' => 'Required',
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

$table;
