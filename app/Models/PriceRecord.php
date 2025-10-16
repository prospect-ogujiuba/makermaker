<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;
use MakerMaker\Helpers\ServiceCatalogHelper;

class PriceRecord extends Model
{
    protected $resource = 'srvc_price_records';

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
        'changed_by'
    ];

    protected $guard = [
        'id',
        'changed_at',
    ];

    protected $with = [
        'servicePrice'
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

    /** PriceRecord belongs to a ServicePrice */
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
     * Smart Price Record recorder - Complete compliance version
     * Tracks ALL fields from ServicePrice for full audit trail
     */
    public static function recordChange($servicePriceId, $changeType, $oldData = [], $newData = [], $reason = 'update', $userID = 1)
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

        $recordData = [
            'service_price_id' => $servicePriceId,
            'change_type' => $determinedChangeType,
            'change_description' => $description,
            'changed_by' => $userID ? $userID : 1,
        ];

        // Map ALL trackable fields (old and new values)
        $trackableFields = [
            'amount' => ['old_amount', 'new_amount'],
            'setup_fee' => ['old_setup_fee', 'new_setup_fee'],
            'currency' => ['old_currency', 'new_currency'],
            'unit' => ['old_unit', 'new_unit'],
            'valid_from' => ['old_valid_from', 'new_valid_from'],
            'valid_to' => ['old_valid_to', 'new_valid_to'],
            'is_current' => ['old_is_current', 'new_is_current'],
            'service_id' => ['old_service_id', 'new_service_id'],
            'pricing_tier_id' => ['old_pricing_tier_id', 'new_pricing_tier_id'],
            'pricing_model_id' => ['old_pricing_model_id', 'new_pricing_model_id'],
            'approval_status' => ['old_approval_status', 'new_approval_status'],
            'approved_by' => ['old_approved_by', 'new_approved_by'],
            'approved_at' => ['old_approved_at', 'new_approved_at'],
        ];

        foreach ($trackableFields as $field => $columns) {
            if (isset($oldData[$field])) {
                $recordData[$columns[0]] = $oldData[$field] ?: null;
            }
            if (isset($newData[$field])) {
                $recordData[$columns[1]] = $newData[$field] ?: null;
            }
        }

        $record = new static();
        return $record->create($recordData);
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

        // If no changes detected, return generic updated
        if (empty($changes)) {
            return 'updated';
        }

        // Multiple fields changed = multi_update
        if (count($changes) > 1) {
            return 'multi_update';
        }

        // Single field changes - determine specific type
        if (isset($changes['amount'])) {
            return 'amount_changed';
        }
        if (isset($changes['setup_fee'])) {
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
        if (isset($changes['service_id'])) {
            return 'tier_changed';
        }
        if (isset($changes['approval_status'])) {
            return 'status_changed';
        }
        if (isset($changes['approved_by'])) {
            return 'approval_changed';
        }
        if (isset($changes['approved_at'])) {
            return 'approval_changed';
        }
        if (isset($changes['valid_from'])) {
            return 'dates_changed';
        }
        if (isset($changes['valid_to'])) {
            return 'dates_changed';
        }
        if (isset($changes['is_current'])) {
            return 'dates_changed';
        }

        // Fallback
        return 'multi_update';
    }

    /**
     * Generate smart description based on changes - with entity names
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
                $oldName = getEntityName(\MakerMaker\Models\Service::class, $changes['service_id']['old']);
                $newName = getEntityName(\MakerMaker\Models\Service::class, $changes['service_id']['new']);
                $parts[] = sprintf('Service: %s → %s', $oldName, $newName);
            }

            if (isset($changes['pricing_tier_id'])) {
                $oldName = getEntityName(\MakerMaker\Models\PricingTier::class, $changes['pricing_tier_id']['old']);
                $newName = getEntityName(\MakerMaker\Models\PricingTier::class, $changes['pricing_tier_id']['new']);
                $parts[] = sprintf('Pricing Tier: %s → %s', $oldName, $newName);
            }

            if (isset($changes['pricing_model_id'])) {
                $oldName = getEntityName(\MakerMaker\Models\PricingModel::class, $changes['pricing_model_id']['old']);
                $newName = getEntityName(\MakerMaker\Models\PricingModel::class, $changes['pricing_model_id']['new']);
                $parts[] = sprintf('Pricing Model: %s → %s', $oldName, $newName);
            }

            // Approval changes - WITH USER NAMES
            if (isset($changes['approval_status'])) {
                $parts[] = sprintf('Status: %s → %s', $changes['approval_status']['old'] ?: 'N/A', $changes['approval_status']['new'] ?: 'N/A');
            }

            if (isset($changes['approved_by'])) {
                $oldName = getUserName($changes['approved_by']['old']);
                $newName = getUserName($changes['approved_by']['new']);
                $parts[] = sprintf('Approved By: %s → %s', $oldName, $newName);
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

    /**
     * Create price change record from old and new data
     */
    public static function createFromChanges($servicePriceId, $changeType, $oldData, $newData, $userId, $customDescription = null)
    {
        $record = new static();

        $record->service_price_id = $servicePriceId;
        $record->changed_by = $userId;

        // Determine change type if 'auto'
        if ($changeType === 'auto') {
            $changes = self::detectChanges($oldData, $newData);
            $changeType = self::determineChangeType($changeType, $changes);
        }

        $record->change_type = $changeType;

        // Populate old/new fields
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
            'approved_at'
        ];

        foreach ($trackableFields as $field) {
            $oldField = 'old_' . $field;
            $newField = 'new_' . $field;

            $record->$oldField = $oldData[$field] ?? null;
            $record->$newField = $newData[$field] ?? null;
        }

        // Generate description
        $changes = self::detectChanges($oldData, $newData);
        $record->change_description = $customDescription ?? self::generateDescription(
            $changeType,
            $changes,
            $oldData,
            $newData,
            null
        );

        $record->save();

        return $record;
    }

    /**
     * Get history for a service price
     */
    public static function getHistoryFor($servicePriceId, $limit = null)
    {
        $query = static::new()
            ->where('service_price_id', $servicePriceId)
            ->orderBy('changed_at', 'DESC');

        if ($limit) {
            $query->take($limit);
        }

        return $query->findAll();
    }

    /**
     * Get recent price changes across all services
     */
    public static function getRecentChanges($days = 30, $limit = 50)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return static::new()
            ->where('changed_at', '>=', $since)
            ->orderBy('changed_at', 'DESC')
            ->take($limit)
            ->findAll();
    }

    /**
     * Get changes by type
     */
    public static function getByChangeType($changeType, $days = null)
    {
        $query = static::new()->where('change_type', $changeType);

        if ($days) {
            $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $query->where('changed_at', '>=', $since);
        }

        return $query->orderBy('changed_at', 'DESC')->findAll();
    }

    /**
     * Get changes by user
     */
    public static function getByUser($userId, $days = null)
    {
        $query = static::new()->where('changed_by', $userId);

        if ($days) {
            $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $query->where('changed_at', '>=', $since);
        }

        return $query->orderBy('changed_at', 'DESC')->findAll();
    }

    /**
     * Get amount increases/decreases
     */
    public static function getAmountChanges($increaseOnly = null, $days = null)
    {
        $query = static::new()
            ->where('change_type', 'IN', ['amount_changed', 'multi_update'])
            ->where('old_amount', 'IS NOT', null)
            ->where('new_amount', 'IS NOT', null);

        if ($increaseOnly === true) {
            $query->appendRawWhere('AND', 'new_amount > old_amount');
        } elseif ($increaseOnly === false) {
            $query->appendRawWhere('AND', 'new_amount < old_amount');
        }

        if ($days) {
            $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));
            $query->where('changed_at', '>=', $since);
        }

        return $query->orderBy('changed_at', 'DESC')->findAll();
    }

    /**
     * Get amount change as decimal
     */
    public function getAmountChange()
    {
        if ($this->old_amount === null || $this->new_amount === null) {
            return null;
        }

        return $this->new_amount - $this->old_amount;
    }

    /**
     * Get amount change as percentage
     */
    public function getAmountChangePercent()
    {
        if ($this->old_amount === null || $this->new_amount === null || $this->old_amount == 0) {
            return null;
        }

        return round((($this->new_amount - $this->old_amount) / $this->old_amount) * 100, 2);
    }

    /**
     * Get setup fee change
     */
    public function getSetupFeeChange()
    {
        if ($this->old_setup_fee === null || $this->new_setup_fee === null) {
            return null;
        }

        return $this->new_setup_fee - $this->old_setup_fee;
    }

    /**
     * Check if this was an increase
     */
    public function wasIncrease()
    {
        $change = $this->getAmountChange();
        return $change !== null && $change > 0;
    }

    /**
     * Check if this was a decrease
     */
    public function wasDecrease()
    {
        $change = $this->getAmountChange();
        return $change !== null && $change < 0;
    }

    /**
     * Get formatted change summary
     */
    public function getChangeSummary()
    {
        $parts = [];

        if ($this->change_type === 'created') {
            return 'Price created';
        }

        if ($this->change_type === 'deleted') {
            return 'Price deleted';
        }

        $amountChange = $this->getAmountChange();
        if ($amountChange !== null) {
            $direction = $amountChange > 0 ? 'increased' : 'decreased';
            $percent = abs($this->getAmountChangePercent());
            $parts[] = "Amount {$direction} by {$percent}%";
        }

        if ($this->old_currency !== $this->new_currency && $this->new_currency) {
            $parts[] = "Currency changed to {$this->new_currency}";
        }

        if ($this->old_approval_status !== $this->new_approval_status && $this->new_approval_status) {
            $parts[] = "Status: {$this->new_approval_status}";
        }

        return !empty($parts) ? implode(', ', $parts) : 'Modified';
    }

    /**
     * Get formatted change date
     */
    public function getFormattedDate($format = 'M j, Y g:i A')
    {
        return date($format, strtotime($this->changed_at));
    }

    /**
     * Get time ago format (e.g., "2 hours ago")
     */
    public function getTimeAgo()
    {
        $timestamp = strtotime($this->changed_at);
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins != 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours != 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days != 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', $timestamp);
        }
    }

    /**
     * Get change icon/indicator
     */
    public function getChangeIcon()
    {
        $icons = [
            'created' => '➕',
            'deleted' => '🗑️',
            'amount_changed' => '💰',
            'currency_changed' => '💱',
            'approval_changed' => '✓',
            'status_changed' => '📊',
            'dates_changed' => '📅',
            'tier_changed' => '📈',
            'model_changed' => '🔄',
            'unit_changed' => '📏',
            'multi_update' => '✏️',
        ];

        return $icons[$this->change_type] ?? '📝';
    }

    /**
     * Export change as array
     */
    public function toChangeArray()
    {
        return [
            'id' => $this->id,
            'change_type' => $this->change_type,
            'summary' => $this->getChangeSummary(),
            'amount_change' => $this->getAmountChange(),
            'amount_change_percent' => $this->getAmountChangePercent(),
            'old_amount' => $this->old_amount,
            'new_amount' => $this->new_amount,
            'currency' => $this->new_currency ?? $this->old_currency,
            'changed_by' => $this->changedBy->display_name ?? 'Unknown',
            'changed_at' => $this->changed_at,
            'formatted_date' => $this->getFormattedDate(),
            'time_ago' => $this->getTimeAgo(),
            'description' => $this->change_description
        ];
    }

    // /**
    //  * Detect what changed between old and new data (helper)
    //  */
    // private static function detectChanges($oldData, $newData)
    // {
    //     $changes = [];

    //     $trackableFields = [
    //         'amount',
    //         'setup_fee',
    //         'currency',
    //         'unit',
    //         'valid_from',
    //         'valid_to',
    //         'is_current',
    //         'service_id',
    //         'pricing_tier_id',
    //         'pricing_model_id',
    //         'approval_status',
    //         'approved_by',
    //         'approved_at'
    //     ];

    //     foreach ($trackableFields as $field) {
    //         $oldValue = $oldData[$field] ?? null;
    //         $newValue = $newData[$field] ?? null;

    //         if ($oldValue != $newValue) {
    //             $changes[$field] = [
    //                 'old' => $oldValue,
    //                 'new' => $newValue
    //             ];
    //         }
    //     }

    //     return $changes;
    // }

    // /**
    //  * Determine specific change type (helper)
    //  */
    // private static function determineChangeType($providedType, $changes)
    // {
    //     if (in_array($providedType, ['created', 'deleted'])) {
    //         return $providedType;
    //     }

    //     if (empty($changes)) {
    //         return 'updated';
    //     }

    //     if (count($changes) > 1) {
    //         return 'multi_update';
    //     }

    //     // Single field changes
    //     $changeMap = [
    //         'amount' => 'amount_changed',
    //         'setup_fee' => 'amount_changed',
    //         'currency' => 'currency_changed',
    //         'unit' => 'unit_changed',
    //         'pricing_tier_id' => 'tier_changed',
    //         'pricing_model_id' => 'model_changed',
    //         'approval_status' => 'status_changed',
    //         'approved_by' => 'approval_changed',
    //         'approved_at' => 'approval_changed',
    //         'valid_from' => 'dates_changed',
    //         'valid_to' => 'dates_changed',
    //         'is_current' => 'dates_changed',
    //     ];

    //     $changedField = array_key_first($changes);
    //     return $changeMap[$changedField] ?? 'multi_update';
    // }

    /**
     * Generate smart description (helper)
     */
    // private static function generateDescription($changeType, $changes, $oldData, $newData, $customReason)
    // {
    //     $parts = [];

    //     if ($changeType === 'created') {
    //         $parts[] = 'Price created';
    //         if (isset($newData['amount'])) {
    //             $parts[] = 'at ' . ServiceCatalogHelper::formatCurrency(
    //                 $newData['amount'],
    //                 $newData['currency'] ?? 'CAD'
    //             );
    //         }
    //     } elseif ($changeType === 'deleted') {
    //         $parts[] = 'Price deleted';
    //     } else {
    //         foreach ($changes as $field => $change) {
    //             switch ($field) {
    //                 case 'amount':
    //                     $parts[] = sprintf(
    //                         'Amount: %s → %s',
    //                         ServiceCatalogHelper::formatCurrency($change['old'], $oldData['currency'] ?? 'CAD'),
    //                         ServiceCatalogHelper::formatCurrency($change['new'], $newData['currency'] ?? 'CAD')
    //                     );
    //                     break;
    //                 case 'setup_fee':
    //                     $parts[] = sprintf(
    //                         'Setup Fee: %s → %s',
    //                         ServiceCatalogHelper::formatCurrency($change['old'], $oldData['currency'] ?? 'CAD'),
    //                         ServiceCatalogHelper::formatCurrency($change['new'], $newData['currency'] ?? 'CAD')
    //                     );
    //                     break;
    //                 case 'currency':
    //                     $parts[] = sprintf('Currency: %s → %s', $change['old'], $change['new']);
    //                     break;
    //                 case 'unit':
    //                     $parts[] = sprintf('Unit: %s → %s', $change['old'], $change['new']);
    //                     break;
    //                 case 'approval_status':
    //                     $parts[] = sprintf('Status: %s → %s', $change['old'], $change['new']);
    //                     break;
    //             }
    //         }
    //     }

    //     if ($customReason) {
    //         $parts[] = $customReason;
    //     }

    //     return implode(' | ', $parts);
    // }
}
