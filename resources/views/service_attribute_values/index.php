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
    'int_val' => [
        'label' => 'Int Val',
        'sort' => 'true'
    ],
    'decimal_val' => [
        'label' => 'Decimal Val',
        'sort' => 'true'
    ],
    'bool_val' => [
        'label' => 'Bool Val',
        'sort' => 'true'
    ],
    'text_val' => [
        'label' => 'Text Val',
        'sort' => 'true'
    ],
    'enum_val' => [
        'label' => 'Enum Val',
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
