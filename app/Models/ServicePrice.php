<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;
use MakerMaker\Helpers\ServiceCatalogHelper;

class ServicePrice extends Model
{
    protected $resource = 'srvc_service_prices';

    protected $fillable = [
        'service_id',
        'pricing_tier_id',
        'pricing_model_id',
        'currency',
        'amount',
        'unit',
        'setup_fee',
        'valid_from',
        'valid_to',
        'is_current',
        'approval_status',
        'approved_by',
        'approved_at',
    ];

    protected $format = [
        'valid_to' => 'convertEmptyToNull',
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $with = [
        'service',
        'pricingTier',
        'pricingModel'
    ];

    /** ServicePrice belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServicePrice belongs to a PricingTier */
    public function pricingTier()
    {
        return $this->belongsTo(PricingTier::class, 'pricing_tier_id');
    }

    /** ServicePrice belongs to a PricingModel */
    public function pricingModel()
    {
        return $this->belongsTo(PricingModel::class, 'pricing_model_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    /**
     * Get all current prices
     */
    public function getCurrent($approvedOnly = true)
    {
        $query = $this->where('is_current', 1)
            ->where('deleted_at', 'IS', null);

        if ($approvedOnly) {
            $query->where('approval_status', 'approved');
        }

        return $query->findAll();
    }

    /**
     * Get prices by approval status
     */
    public function getByStatus($status)
    {
        $validStatuses = ['draft', 'pending', 'approved', 'rejected'];

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid approval status: {$status}");
        }

        return $this->where('approval_status', $status)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get pending approval prices
     */
    public function getPendingApproval()
    {
        return $this->getByStatus('pending')->get();
    }

    /**
     * Get prices by currency
     */
    public function getByCurrency($currency)
    {
        return $this->where('currency', $currency)
            ->where('deleted_at', 'IS', null)
            ->findAll();
    }

    /**
     * Get prices valid on a specific date
     */
    public function getValidOnDate($date, $serviceId = null)
    {
        $query = $this->where('valid_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->where('valid_to', '>=', $date)
                    ->orWhere('valid_to', 'IS', null);
            })
            ->where('deleted_at', 'IS', null);

        if ($serviceId) {
            $query->where('service_id', $serviceId);
        }

        return $query->findAll();
    }

    /**
     * Get current price for service/tier/model combination
     */
    public function getCurrentPriceFor($serviceId, $tierId, $modelId)
    {
        return $this->where('service_id', $serviceId)
            ->where('pricing_tier_id', $tierId)
            ->where('pricing_model_id', $modelId)
            ->where('is_current', 1)
            ->where('approval_status', 'approved')
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Check if price is currently valid
     */
    public function isValid()
    {
        $now = date('Y-m-d H:i:s');

        $afterStart = $this->valid_from <= $now;
        $beforeEnd = $this->valid_to === null || $this->valid_to >= $now;

        return $afterStart && $beforeEnd;
    }

    /**
     * Check if price is approved
     */
    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Check if price is pending approval
     */
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Check if price can be edited
     */
    public function canEdit()
    {
        return in_array($this->approval_status, ['draft', 'rejected']);
    }

    /**
     * Submit for approval
     */
    public function submitForApproval()
    {
        if ($this->approval_status === 'approved') {
            throw new \RuntimeException("Cannot resubmit already approved price");
        }

        $this->approval_status = 'pending';
        $this->approved_by = null;
        $this->approved_at = null;

        return $this->update(['approval_status', 'approved_by', 'approved_at']);
    }

    /**
     * Approve price
     */
    public function approve($userId)
    {
        if ($this->approval_status !== 'pending') {
            throw new \RuntimeException("Only pending prices can be approved");
        }

        $this->approval_status = 'approved';
        $this->approved_by = $userId;
        $this->approved_at = date('Y-m-d H:i:s');

        return $this->update(['approval_status', 'approved_by', 'approved_at']);
    }

    /**
     * Reject price
     */
    public function reject($userId)
    {
        if ($this->approval_status !== 'pending') {
            throw new \RuntimeException("Only pending prices can be rejected");
        }

        $this->approval_status = 'rejected';
        $this->approved_by = $userId;
        $this->approved_at = date('Y-m-d H:i:s');

        return $this->update(['approval_status', 'approved_by', 'approved_at']);
    }

    /**
     * Set as current price (marks others as not current)
     */
    public function setAsCurrent()
    {
        if (!$this->isApproved()) {
            throw new \RuntimeException("Only approved prices can be set as current");
        }

        // Mark other prices for same service/tier/model as not current
        $wpdb = $this->getQuery()->getWpdb();
        $table = $this->getTable();

        $wpdb->query($wpdb->prepare(
            "UPDATE {$table} SET is_current = 0 
         WHERE service_id = %d 
         AND pricing_tier_id = %d 
         AND pricing_model_id = %d 
         AND id != %d
         AND deleted_at IS NULL",
            $this->service_id,
            $this->pricing_tier_id,
            $this->pricing_model_id,
            $this->id
        ));

        $this->is_current = 1;
        return $this->update(['is_current']);
    }

    /**
     * Unset as current
     */
    public function unsetCurrent()
    {
        $this->is_current = 0;
        return $this->update(['is_current']);
    }

    /**
     * Calculate total price (amount + setup fee)
     */
    public function getTotalPrice()
    {
        return $this->amount + $this->setup_fee;
    }

    /**
     * Get formatted price string
     */
    public function getFormattedPrice($includeSetup = false)
    {
        $currency = $this->currency ?? 'CAD';
        $symbol = ServiceCatalogHelper::getCurrencySymbol($currency);

        $price = $symbol . number_format($this->amount, 2);

        if ($this->unit) {
            $price .= ' / ' . $this->unit;
        }

        if ($includeSetup && $this->setup_fee > 0) {
            $price .= ' (+ ' . $symbol . number_format($this->setup_fee, 2) . ' setup)';
        }

        return $price;
    }

    /**
     * Get validity period as formatted string
     */
    public function getValidityPeriod()
    {
        $from = date('M j, Y', strtotime($this->valid_from));
        $to = $this->valid_to ? date('M j, Y', strtotime($this->valid_to)) : 'Ongoing';

        return "{$from} - {$to}";
    }

    /**
     * Extend validity period
     */
    public function extendValidity($newEndDate)
    {
        if ($this->valid_to && $newEndDate <= $this->valid_to) {
            throw new \InvalidArgumentException("New end date must be after current end date");
        }

        $this->valid_to = $newEndDate;
        return $this->update(['valid_to']);
    }

    /**
     * Check if price will expire soon
     */
    public function expiresWithinDays($days = 30)
    {
        if ($this->valid_to === null) {
            return false;
        }

        $expiryTimestamp = strtotime($this->valid_to);
        $thresholdTimestamp = strtotime("+{$days} days");

        return $expiryTimestamp <= $thresholdTimestamp;
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiry()
    {
        if ($this->valid_to === null) {
            return null;
        }

        $now = time();
        $expiry = strtotime($this->valid_to);
        $diff = $expiry - $now;

        return max(0, ceil($diff / 86400)); // 86400 seconds in a day
    }

    /**
     * Clone price for new tier/model
     */
    public function cloneForTierModel($newTierId = null, $newModelId = null)
    {
        $clone = new static();

        $clone->service_id = $this->service_id;
        $clone->pricing_tier_id = $newTierId ?? $this->pricing_tier_id;
        $clone->pricing_model_id = $newModelId ?? $this->pricing_model_id;
        $clone->currency = $this->currency;
        $clone->amount = $this->amount;
        $clone->unit = $this->unit;
        $clone->setup_fee = $this->setup_fee;
        $clone->valid_from = date('Y-m-d H:i:s');
        $clone->valid_to = $this->valid_to;
        $clone->is_current = 0; // Clones start as not current
        $clone->approval_status = 'draft'; // Clones start as draft

        return $clone;
    }

    /**
     * Convert to different currency (requires CurrencyRate)
     */
    public function convertToCurrency($targetCurrency, $effectiveDate = null)
    {
        if ($this->currency === $targetCurrency) {
            return $this;
        }

        $effectiveDate = $effectiveDate ?? date('Y-m-d');

        $rate = CurrencyRate::new()
            ->where('from_currency', $this->currency)
            ->where('to_currency', $targetCurrency)
            ->where('effective_date', '<=', $effectiveDate)
            ->where('deleted_at', 'IS', null)
            ->orderBy('effective_date', 'DESC')
            ->first();

        if (!$rate) {
            throw new \RuntimeException(
                "No exchange rate found for {$this->currency} to {$targetCurrency} on {$effectiveDate}"
            );
        }

        $clone = $this->cloneForTierModel();
        $clone->currency = $targetCurrency;
        $clone->amount = round($this->amount * $rate->exchange_rate, 2);
        $clone->setup_fee = round($this->setup_fee * $rate->exchange_rate, 2);

        return $clone;
    }

    /**
     * Get price change history
     */
    public function getHistory()
    {
        return PriceRecord::new()
            ->where('service_price_id', $this->id)
            ->orderBy('changed_at', 'DESC')
            ->findAll();
    }

    /**
     * Log price change
     */
    public function logChange($changeType, $oldData, $newData, $userId, $description = null)
    {
        return PriceRecord::createFromChanges(
            $this->id,
            $changeType,
            $oldData,
            $newData,
            $userId,
            $description
        );
    }

    /**
     * Validate price data
     */
    public function validatePrice()
    {
        $errors = [];

        if ($this->amount < 0) {
            $errors[] = "Amount must be non-negative";
        }

        if ($this->setup_fee < 0) {
            $errors[] = "Setup fee must be non-negative";
        }

        if ($this->valid_to && $this->valid_to <= $this->valid_from) {
            $errors[] = "Valid-to date must be after valid-from date";
        }

        if (!preg_match('/^[A-Z]{3}$/', $this->currency)) {
            $errors[] = "Currency must be a 3-letter code (e.g., CAD, USD)";
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Soft delete
     */
    public function softDelete()
    {
        if ($this->is_current == 1 && $this->approval_status === 'approved') {
            throw new \RuntimeException(
                "Cannot delete current approved price. Set another price as current first."
            );
        }

        $this->deleted_at = date('Y-m-d H:i:s');
        return $this->update(['deleted_at']);
    }

    /**
     * Restore from soft delete
     */
    public function restore()
    {
        $this->deleted_at = null;
        return $this->update(['deleted_at']);
    }

    /**
     * Get approval status badge
     */
    public function getStatusBadge()
    {
        $badges = [
            'draft' => '<span class="status-badge draft">Draft</span>',
            'pending' => '<span class="status-badge pending">Pending Approval</span>',
            'approved' => '<span class="status-badge approved">Approved</span>',
            'rejected' => '<span class="status-badge rejected">Rejected</span>',
        ];

        return $badges[$this->approval_status] ?? '<span class="status-badge unknown">Unknown</span>';
    }
}
