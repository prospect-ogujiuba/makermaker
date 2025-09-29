<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class PriceHistory extends Model
{
    protected $resource = 'srvc_price_history';

    protected $fillable = [
        'service_price_id',
        'change_type',
        'old_amount',
        'new_amount',
        'old_setup_fee',
        'new_setup_fee',
        'old_currency',
        'new_currency',
        'old_unit',
        'new_unit',
        'old_valid_from',
        'new_valid_from',
        'old_valid_to',
        'new_valid_to',
        'old_is_current',
        'new_is_current',
        'old_service_id',
        'new_service_id',
        'old_pricing_tier_id',
        'new_pricing_tier_id',
        'old_pricing_model_id',
        'new_pricing_model_id',
        'old_approval_status',
        'new_approval_status',
        'old_approved_by',
        'new_approved_by',
        'old_approved_at',
        'new_approved_at',
        'change_description',
    ];

    protected $guard = [
        'id',
        'changed_at',
        'changed_by',
    ];

    protected $format = [
        'old_amount' => 'convertEmptyToNull',
        'new_amount' => 'convertEmptyToNull',
        'old_setup_fee' => 'convertEmptyToNull',
        'new_setup_fee' => 'convertEmptyToNull',
        'old_valid_from' => 'convertEmptyToNull',
        'new_valid_from' => 'convertEmptyToNull',
        'old_valid_to' => 'convertEmptyToNull',
        'new_valid_to' => 'convertEmptyToNull',
        'old_approved_at' => 'convertEmptyToNull',
        'new_approved_at' => 'convertEmptyToNull',
    ];

    /** PriceHistory belongs to a ServicePrice */
    public function servicePrice()
    {
        return $this->belongsTo(ServicePrice::class, 'service_price_id');
    }

    /** Changed by WP user */
    public function changedBy()
    {
        return $this->belongsTo(WPUser::class, 'changed_by');
    }

    /** Old approved by WP user */
    public function oldApprovedBy()
    {
        return $this->belongsTo(WPUser::class, 'old_approved_by');
    }

    /** New approved by WP user */
    public function newApprovedBy()
    {
        return $this->belongsTo(WPUser::class, 'new_approved_by');
    }

    /**
     * Smart price history recorder - Complete compliance version
     * Tracks ALL fields from ServicePrice for full audit trail
     */
    public static function recordChange($servicePriceId, $changeType, $oldData = [], $newData = [], $reason = null, $userID)
    {
        // Detect actual changes
        $changes = self::detectChanges($oldData, $newData);

        // If no meaningful changes and not a create/delete operation, do nothing
        if (empty($changes) && !in_array($changeType, ['created', 'deleted'])) {
            return null;
        }

        // Auto-determine change type if it's generic
        $determinedChangeType = self::determineChangeType($changeType, $changes);

        // Generate smart description
        $description = self::generateDescription($determinedChangeType, $changes, $oldData, $newData, $reason);

        $historyData = [
            'service_price_id' => $servicePriceId,
            'change_type' => $determinedChangeType,
            'change_description' => $description,
            'changed_by' => $userID ? $userID : 1,
        ];

        // Map ALL trackable fields (old and new values)
        $trackableFields = [
            // Financial
            'amount' => ['old_amount', 'new_amount'],
            'setup_fee' => ['old_setup_fee', 'new_setup_fee'],
            'currency' => ['old_currency', 'new_currency'],
            'unit' => ['old_unit', 'new_unit'],
            // Temporal
            'valid_from' => ['old_valid_from', 'new_valid_from'],
            'valid_to' => ['old_valid_to', 'new_valid_to'],
            'is_current' => ['old_is_current', 'new_is_current'],
            // Relationships
            'service_id' => ['old_service_id', 'new_service_id'],
            'pricing_tier_id' => ['old_pricing_tier_id', 'new_pricing_tier_id'],
            'pricing_model_id' => ['old_pricing_model_id', 'new_pricing_model_id'],
            // Approval
            'approval_status' => ['old_approval_status', 'new_approval_status'],
            'approved_by' => ['old_approved_by', 'new_approved_by'],
            'approved_at' => ['old_approved_at', 'new_approved_at'],
        ];

        foreach ($trackableFields as $field => $columns) {
            if (isset($oldData[$field])) {
                $historyData[$columns[0]] = $oldData[$field] ?: null;
            }
            if (isset($newData[$field])) {
                $historyData[$columns[1]] = $newData[$field] ?: null;
            }
        }

        $history = new static();
        return $history->create($historyData);
    }

    /**
     * Detect which fields actually changed - Complete version
     */
    private static function detectChanges($oldData, $newData)
    {
        $changes = [];
        $trackableFields = [
            'amount',
            'setup_fee',
            'currency',
            'unit',
            'valid_from',
            'valid_to',
            'is_current',
            'service_id',
            'pricing_tier_id',
            'pricing_model_id',
            'approval_status',
            'approved_by',
            'approved_at',
            'changed_by'
        ];

        foreach ($trackableFields as $field) {
            $oldValue = $oldData[$field] ?? null;
            $newValue = $newData[$field] ?? null;

            // Normalize for comparison (handle type casting)
            if ($oldValue != $newValue) {
                $changes[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Determine specific change type based on what changed
     */
    private static function determineChangeType($providedType, $changes)
    {
        // If specific type provided and it's create/delete, use it
        if (in_array($providedType, ['created', 'deleted'])) {
            return $providedType;
        }

        // Auto-determine based on priority
        if (isset($changes['amount']) && count($changes) == 1) {
            return 'amount_changed';
        }

        if (isset($changes['currency'])) {
            return 'currency_changed';
        }

        if (isset($changes['unit'])) {
            return 'unit_changed';
        }

        if (isset($changes['pricing_tier_id'])) {
            return 'tier_changed';
        }

        if (isset($changes['pricing_model_id'])) {
            return 'model_changed';
        }

        if (isset($changes['approval_status']) || isset($changes['approved_by']) || isset($changes['approved_at'])) {
            return 'approval_changed';
        }

        if (isset($changes['valid_from']) || isset($changes['valid_to']) || isset($changes['is_current'])) {
            return 'dates_changed';
        }

        // Multiple fields or other changes
        return 'multi_update';
    }

    /**
     * Generate smart description based on changes - Complete version
     */
    private static function generateDescription($changeType, $changes, $oldData, $newData, $customReason)
    {
        $parts = [];

        // Handle create/delete first
        if ($changeType === 'created') {
            $parts[] = 'Price created';
            if (isset($newData['amount'])) {
                $parts[] = 'at ' . ($newData['currency'] ?? 'CAD') . ' $' . number_format($newData['amount'], 2);
            }
        } elseif ($changeType === 'deleted') {
            $parts[] = 'Price deleted';
        } else {
            // Financial changes
            if (isset($changes['amount'])) {
                $oldFormatted = $changes['amount']['old'] ? '$' . number_format($changes['amount']['old'], 2) : 'N/A';
                $newFormatted = $changes['amount']['new'] ? '$' . number_format($changes['amount']['new'], 2) : 'N/A';
                $parts[] = sprintf('Amount: %s → %s', $oldFormatted, $newFormatted);
            }

            if (isset($changes['setup_fee'])) {
                $oldFormatted = $changes['setup_fee']['old'] ? '$' . number_format($changes['setup_fee']['old'], 2) : 'N/A';
                $newFormatted = $changes['setup_fee']['new'] ? '$' . number_format($changes['setup_fee']['new'], 2) : 'N/A';
                $parts[] = sprintf('Setup Fee: %s → %s', $oldFormatted, $newFormatted);
            }

            if (isset($changes['currency'])) {
                $parts[] = sprintf('Currency: %s → %s', $changes['currency']['old'] ?: 'N/A', $changes['currency']['new'] ?: 'N/A');
            }

            if (isset($changes['unit'])) {
                $parts[] = sprintf('Unit: %s → %s', $changes['unit']['old'] ?: 'N/A', $changes['unit']['new'] ?: 'N/A');
            }

            // Temporal changes
            if (isset($changes['valid_from'])) {
                $parts[] = sprintf('Valid From: %s → %s', $changes['valid_from']['old'] ?: 'N/A', $changes['valid_from']['new'] ?: 'N/A');
            }

            if (isset($changes['valid_to'])) {
                $parts[] = sprintf('Valid To: %s → %s', $changes['valid_to']['old'] ?: 'N/A', $changes['valid_to']['new'] ?: 'N/A');
            }

            if (isset($changes['is_current'])) {
                $parts[] = sprintf('Is Current: %s → %s', $changes['is_current']['old'] ? 'Yes' : 'No', $changes['is_current']['new'] ? 'Yes' : 'No');
            }

            // Relationship changes
            if (isset($changes['service_id'])) {
                $parts[] = sprintf('Service ID: %s → %s', $changes['service_id']['old'] ?: 'N/A', $changes['service_id']['new'] ?: 'N/A');
            }

            if (isset($changes['pricing_tier_id'])) {
                $parts[] = sprintf('Pricing Tier ID: %s → %s', $changes['pricing_tier_id']['old'] ?: 'N/A', $changes['pricing_tier_id']['new'] ?: 'N/A');
            }

            if (isset($changes['pricing_model_id'])) {
                $parts[] = sprintf('Pricing Model ID: %s → %s', $changes['pricing_model_id']['old'] ?: 'N/A', $changes['pricing_model_id']['new'] ?: 'N/A');
            }

            // Approval changes
            if (isset($changes['approval_status'])) {
                $parts[] = sprintf('Status: %s → %s', $changes['approval_status']['old'] ?: 'N/A', $changes['approval_status']['new'] ?: 'N/A');
            }

            if (isset($changes['approved_by'])) {
                $parts[] = sprintf('Approved By: User #%s → User #%s', $changes['approved_by']['old'] ?: 'N/A', $changes['approved_by']['new'] ?: 'N/A');
            }

            if (isset($changes['approved_at'])) {
                $parts[] = sprintf('Approved At: %s → %s', $changes['approved_at']['old'] ?: 'N/A', $changes['approved_at']['new'] ?: 'N/A');
            }
        }

        // Add custom reason if provided
        if ($customReason) {
            $parts[] = $customReason;
        }

        return implode(' | ', $parts);
    }
}
