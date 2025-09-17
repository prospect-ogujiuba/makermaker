<?php

use MakerMaker\Models\ServicePrice;
use MakerMaker\Models\ServicePricingModel;
use MakerMaker\Models\ServicePricingTier;

$table = tr_table(ServicePrice::class);

$table->setBulkActions(tr_form()->useConfirm(), []);

$table->setColumns([
    'service.name' => [
        'label' => 'Service',
        'sort' => true,
        'actions' => ['edit', 'view', 'delete'],
        'callback' => function ($value, $item) {
            $service = $item->service;
            return $service ? $service->name : '<em>Unknown Service</em>';
        }
    ],

    'pricing_tier.name' => [
        'label' => 'Tier',
        'sort' => true,
        'callback' => function ($value, $item) {
            $tier = $item->pricingTier;
            return $tier ? $tier->name : '<em>Unknown Tier</em>';
        }
    ],

    'pricing_model.name' => [
        'label' => 'Model',
        'sort' => true,
        'callback' => function ($value, $item) {
            $model = $item->pricingModel;
            return $model ? $model->name : '<em>Unknown Model</em>';
        }
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

    'effective_from' => [
        'label' => 'Effective Period',
        'sort' => true,
        'callback' => function ($value, $item) {
            $from = date('M j, Y', strtotime($value));
            $to = $item->effective_to ? date('M j, Y', strtotime($item->effective_to)) : 'Ongoing';

            $today = date('Y-m-d');
            $status = '';

            if ($item->effective_from > $today) {
                $status = '<span style="color: #0073aa;">Future</span>';
            } elseif ($item->effective_to && $item->effective_to < $today) {
                $status = '<span style="color: #d63638;">Expired</span>';
            } else {
                $status = '<span style="color: #00a32a;">Active</span>';
            }

            return $from . ' - ' . $to . '<br>' . $status;
        }
    ],

    'currency' => [
        'label' => 'Currency',
        'sort' => true,
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
        'sort' => true
    ]
], 'service.name')->setOrder('effective_from', 'DESC')->render();
