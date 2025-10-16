<?php

/**
 * ServicePrice Index View
 */

use MakerMaker\Models\ServicePrice;

$table = tr_table(ServicePrice::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete']
    ],
    'pricingTier.name' => [
        'label' => 'Tier',
        'sort' => true,
        'callback' => function ($value) {
            return "<span class=\"badge badge-info\">{$value}</span>";
        }
    ],
    'pricingModel.name' => [
        'label' => 'Model',
        'sort' => true,
        'callback' => function ($value) {
            return "<span class=\"badge badge-secondary\">{$value}</span>";
        }
    ],
    'amount' => [
        'label' => 'Price',
        'sort' => true,
        'callback' => function ($value, $item) {
            if ($value === null || $value == 0) {
                return '<span class="badge badge-warning">Quote Required</span>';
            }

            $symbol = match ($item->currency) {
                'USD' => '$',
                'CAD' => 'C$',
                'EUR' => '€',
                'GBP' => '£',
                'AUD' => 'A$',
                'JPY' => '¥',
                default => $item->currency . ' '
            };

            $formatted = $symbol . number_format($value, 2);

            if ($item->unit) {
                $formatted .= ' <small class="text-muted">/ ' . $item->unit . '</small>';
            }

            if ($item->setup_fee && $item->setup_fee > 0) {
                $formatted .= '<br><small class="text-info">Setup: ' . $symbol . number_format($item->setup_fee, 2) . '</small>';
            }

            return $formatted;
        }
    ],
    'validity_period' => [
        'label' => 'Validity',
        'callback' => function ($value, $item) {
            $from = date('M j, Y', strtotime($item->valid_from));
            $to = $item->valid_to ? date('M j, Y', strtotime($item->valid_to)) : 'No expiry';
            return "{$from} - {$to}";
        }
    ],
    'status_flags' => [
        'label' => 'Status',
         'callback' => function ($item, $value) {
            return $value->approval_status == 'approved' ?
                "<i class='bi bi-check' style='color: green;'></i>" :
                "<i class='bi bi-x' style='color: red;'></i>";
        }
    ],
    'currency' => [
        'label' => 'Currency',
        'sort' => true,
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
        'sort' => true
    ]
], 'service.name')->setOrder('id', 'DESC')->render();

$table;
