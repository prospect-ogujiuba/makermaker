<?php

/**
 * PriceHistory Index View - Complete Compliance Version
 */

use MakerMaker\Models\PriceHistory;

$table = tr_table(PriceHistory::class);

$table->setBulkActions(tr_form()->useConfirm(), []); // No bulk actions for read-only records

$table->setColumns([
    'id' => [
        'label' => 'ID',
        'sort' => true,
    ],
    'servicePrice.service.name' => [
        'label' => 'Service',
        'sort' => true,
        'actions' => ['view']
    ],
    'change_type' => [
        'label' => 'Change',
        'sort' => true
    ],
    'change_description' => [
        'label' => 'Summary',
        'callback' => function ($value) {
            if (!$value) return '—';

            // Truncate long descriptions
            if (strlen($value) > 100) {
                return '<span class="text-muted" title="' . esc_attr($value) . '">' .
                    esc_html(substr($value, 0, 97)) . '...</span>';
            }

            return '<span class="text-muted">' . esc_html($value) . '</span>';
        }
    ],
    'changed_at' => [
        'label' => 'Changed At',
        'sort' => true,
        'callback' => function ($value) {
            return date('M j, Y<\b\r>g:i A', strtotime($value));
        }
    ],
    'changedBy.display_name' => [
        'label' => 'Changed By',
        'callback' => function ($value, $item) {
            return $item->changedBy ? $item->changedBy->display_name : '<em>System</em>';
        }
    ],
], 'changed_at')->setOrder('changed_at', 'DESC');

$table->render();
