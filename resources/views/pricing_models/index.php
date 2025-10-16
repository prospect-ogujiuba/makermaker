<?php

/**
 * PricingModel Index View
 */

use MakerMaker\Models\PricingModel;

$table = tr_table(PricingModel::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'name' => [
        'label' => 'Name',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
    ],

    'code' => [
        'label' => 'Code',
        'sort' => true,
                'callback' => function($value) {
            return "<code>{$value}</code>";
        }
    ],

    'description' => [
        'label' => 'Description',
        'sort' => true,
    ],

    'is_time_based' => [
        'label' => 'Time Based',
        'sort' => true,
                 'callback' => function ($value) {
            return $value ?
                "<i class='bi bi-check' style='color: green;'></i>" :
                "<i class='bi bi-x' style='color: red;'></i>";
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
], 'name')->setOrder('id', 'DESC')->render();
