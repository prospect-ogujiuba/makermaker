<?php

/**
 * CurrencyRate Index View
 */

use MakerMaker\Models\CurrencyRate;

$table = tr_table(CurrencyRate::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'from_currency' => [
        'label' => 'From',
        'sort' => 'true',
        'actions' => ['edit', 'view', 'delete']
    ],
    'to_currency' => [
        'label' => 'To',
        'sort' => 'true'
    ],
    'exchange_rate' => [
        'label' => 'Rate',
        'sort' => 'true',
        'callback' => function ($value) {
            return number_format($value, 6);
        }
    ],
    'effective_date' => [
        'label' => 'Effective Date',
        'sort' => 'true'
    ],
    'source' => [
        'label' => 'Source',
        'sort' => 'true',
        'callback' => function ($value) {
            return "<code>{$value}</code>";
        }
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
], 'from_currency')->setOrder('id', 'DESC')->render();

$table;
