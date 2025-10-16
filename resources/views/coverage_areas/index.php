<?php

/**
 * CoverageArea Index View
 */

use MakerMaker\Models\CoverageArea;

$table = tr_table(CoverageArea::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'code' => [
        'label' => 'Code',
        'sort' => true,
                'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
    ],
    'country_code' => [
        'label' => 'Country',
        'sort' => true
    ],
    'region_type' => [
        'label' => 'Region Type',
        'sort' => true
    ],
    'timezone' => [
        'label' => 'Timezone',
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
