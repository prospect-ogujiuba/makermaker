<?php

/**
 * ServiceAttributeValue Index View
 */

use MakerMaker\Models\ServiceAttributeValue;

$table = tr_table(ServiceAttributeValue::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Name',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete'],
    ],
    'attributeDefinition.label' => [
        'label' => 'Parent ID',
        'sort' => 'true'
    ],
    'value' => [
        'label' => 'Value',
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
        'label' => 'Last Updated By',
    ],
], 'name')->setOrder('service_id', 'DESC')->render();

$table;
