<?php

/**
 * ServiceBundle Index View
 */

use MakerMaker\Models\ServiceBundle;

$table = tr_table(ServiceBundle::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Bundle Name',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete']
    ],
    'short_desc' => [
        'label' => 'Short Description',
        'sort' => 'true'
    ],
    'slug' => [
        'label' => 'Slug',
        'sort' => 'true'
    ],
    'services' => [
        'label' => 'Services',
        'callback' => function ($value, $item) {
            return count((array)$item->services);
        }
    ],
    'is_active' => [
        'label' => 'Is Active',
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
