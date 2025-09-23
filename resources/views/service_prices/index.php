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
        'sort' => true
    ],
    'pricingModel.name' => [
        'label' => 'Model',
        'sort' => true
    ],
    'amount' => [
        'label' => 'Price',
        'sort' => true,
        'callback' => function ($value, $item) {
            if ($value === null || $value == 0) {
                return '<em style="color: #666;">Quote Required</em>';
            }

            $symbol = match ($item->currency) {
                'USD' => '$',
                'CAD' => 'C$',
                'EUR' => '€',
                'GBP' => '£',
                default => $item->currency . ' '
            };

            $formatted = $symbol . number_format($value, 2);

            if ($item->unit) {
                $formatted .= ' <small style="color: #666;">per ' . $item->unit . '</small>';
            }

            if ($item->setup_fee && $item->setup_fee > 0) {
                $formatted .= '<br><small style="color: #0073aa;">Setup: ' . $symbol . number_format($item->setup_fee, 2) . '</small>';
            }

            return $formatted;
        }
    ],
    'currency' => [
        'label' => 'Currency',
        'sort' => true
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
