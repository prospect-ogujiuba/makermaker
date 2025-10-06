<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class CurrencyRate extends Model
{
    protected $resource = 'srvc_currency_rates';

    protected $fillable = [
        'from_currency',
        'to_currency',
        'exchange_rate',
        'effective_date',
        'source'
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
        'servicePrices'
    ];

    // Service prices using this pricing model
    public function servicePrices()
    {
        return $this->hasMany(\MakerMaker\Models\ServicePrice::class, 'pricing_model_id');
    }

    // User who created this record
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    // User who last updated this record
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    /**
     * Get rate for currency pair on specific date
     */
    public static function getRateForDate($fromCurrency, $toCurrency, $date = null)
    {
        $date = $date ?? date('Y-m-d');

        return static::new()
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '<=', $date)
            ->where('deleted_at', 'IS', null)
            ->orderBy('effective_date', 'DESC')
            ->first();
    }

    /**
     * Get latest rate for currency pair
     */
    public static function getLatestRate($fromCurrency, $toCurrency)
    {
        return static::getRateForDate($fromCurrency, $toCurrency);
    }

    /**
     * Get all rates for a specific date
     */
    public static function getRatesForDate($date = null)
    {
        $date = $date ?? date('Y-m-d');

        // Get most recent rate for each currency pair as of date
        $wpdb = static::new()->getQuery()->getWpdb();
        $table = static::new()->getTable();

        $sql = $wpdb->prepare(
            "SELECT cr1.* 
         FROM {$table} cr1
         INNER JOIN (
             SELECT from_currency, to_currency, MAX(effective_date) as max_date
             FROM {$table}
             WHERE effective_date <= %s AND deleted_at IS NULL
             GROUP BY from_currency, to_currency
         ) cr2 ON cr1.from_currency = cr2.from_currency 
             AND cr1.to_currency = cr2.to_currency 
             AND cr1.effective_date = cr2.max_date
         WHERE cr1.deleted_at IS NULL
         ORDER BY cr1.from_currency, cr1.to_currency",
            $date
        );

        return $wpdb->get_results($sql);
    }

    /**
     * Convert amount between currencies
     */
    public static function convert($amount, $fromCurrency, $toCurrency, $date = null)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $rate = static::getRateForDate($fromCurrency, $toCurrency, $date);

        if (!$rate) {
            throw new \RuntimeException(
                "No exchange rate found for {$fromCurrency} to {$toCurrency}" .
                    ($date ? " on {$date}" : "")
            );
        }

        return round($amount * $rate->exchange_rate, 2);
    }

    /**
     * Get inverse rate (e.g., if you have CAD->USD, get USD->CAD)
     */
    public function getInverseRate()
    {
        if ($this->exchange_rate == 0) {
            throw new \RuntimeException("Cannot calculate inverse of zero rate");
        }

        return round(1 / $this->exchange_rate, 6);
    }

    /**
     * Create inverse rate record
     */
    public function createInverseRecord($userId)
    {
        $inverse = new static();
        $inverse->from_currency = $this->to_currency;
        $inverse->to_currency = $this->from_currency;
        $inverse->exchange_rate = $this->getInverseRate();
        $inverse->effective_date = $this->effective_date;
        $inverse->source = $this->source . '_inverse';
        $inverse->created_by = $userId;
        $inverse->updated_by = $userId;

        return $inverse->save();
    }

    /**
     * Get all rates from a specific currency
     */
    public static function getRatesFrom($currency, $date = null)
    {
        $date = $date ?? date('Y-m-d');

        return static::new()
            ->where('from_currency', $currency)
            ->where('effective_date', '<=', $date)
            ->where('deleted_at', 'IS', null)
            ->orderBy('effective_date', 'DESC')
            ->findAll();
    }

    /**
     * Get all rates to a specific currency
     */
    public static function getRatesTo($currency, $date = null)
    {
        $date = $date ?? date('Y-m-d');

        return static::new()
            ->where('to_currency', $currency)
            ->where('effective_date', '<=', $date)
            ->where('deleted_at', 'IS', null)
            ->orderBy('effective_date', 'DESC')
            ->findAll();
    }

    /**
     * Get rate history for a currency pair
     */
    public static function getRateHistory($fromCurrency, $toCurrency, $days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));

        return static::new()
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', '>=', $since)
            ->where('deleted_at', 'IS', null)
            ->orderBy('effective_date', 'DESC')
            ->findAll();
    }

    /**
     * Get rate by source
     */
    public static function getRatesBySource($source, $date = null)
    {
        $query = static::new()
            ->where('source', $source)
            ->where('deleted_at', 'IS', null)
            ->orderBy('effective_date', 'DESC');

        if ($date) {
            $query->where('effective_date', '<=', $date);
        }

        return $query->findAll();
    }

    /**
     * Check if rate exists for currency pair and date
     */
    public static function rateExists($fromCurrency, $toCurrency, $effectiveDate)
    {
        $existing = static::new()
            ->where('from_currency', $fromCurrency)
            ->where('to_currency', $toCurrency)
            ->where('effective_date', $effectiveDate)
            ->where('deleted_at', 'IS', null)
            ->first();

        return $existing !== null;
    }

    /**
     * Get rate change over time
     */
    public static function getRateChange($fromCurrency, $toCurrency, $days = 30)
    {
        $today = static::getLatestRate($fromCurrency, $toCurrency);

        if (!$today) {
            return null;
        }

        $pastDate = date('Y-m-d', strtotime("-{$days} days"));
        $past = static::getRateForDate($fromCurrency, $toCurrency, $pastDate);

        if (!$past) {
            return null;
        }

        $change = $today->exchange_rate - $past->exchange_rate;
        $changePercent = round(($change / $past->exchange_rate) * 100, 2);

        return [
            'from' => $fromCurrency,
            'to' => $toCurrency,
            'current_rate' => $today->exchange_rate,
            'past_rate' => $past->exchange_rate,
            'change' => $change,
            'change_percent' => $changePercent,
            'days' => $days,
            'direction' => $change > 0 ? 'increased' : ($change < 0 ? 'decreased' : 'unchanged')
        ];
    }

    /**
     * Calculate cross rate (e.g., EUR to GBP via CAD)
     */
    public static function calculateCrossRate($fromCurrency, $toCurrency, $viaCurrency = 'CAD', $date = null)
    {
        $rate1 = static::getRateForDate($fromCurrency, $viaCurrency, $date);
        $rate2 = static::getRateForDate($viaCurrency, $toCurrency, $date);

        if (!$rate1 || !$rate2) {
            throw new \RuntimeException(
                "Cannot calculate cross rate {$fromCurrency} to {$toCurrency} via {$viaCurrency}"
            );
        }

        return round($rate1->exchange_rate * $rate2->exchange_rate, 6);
    }

    /**
     * Get formatted rate display
     */
    public function getFormattedRate($decimals = 4)
    {
        return sprintf(
            '1 %s = %s %s',
            $this->from_currency,
            number_format($this->exchange_rate, $decimals),
            $this->to_currency
        );
    }

    /**
     * Get rate with effective date
     */
    public function getFormattedRateWithDate($decimals = 4)
    {
        return sprintf(
            '%s (as of %s)',
            $this->getFormattedRate($decimals),
            date('M j, Y', strtotime($this->effective_date))
        );
    }

    /**
     * Check if rate is current (within last 7 days)
     */
    public function isCurrent($days = 7)
    {
        $threshold = date('Y-m-d', strtotime("-{$days} days"));
        return $this->effective_date >= $threshold;
    }

    /**
     * Check if rate is outdated
     */
    public function isOutdated($days = 30)
    {
        return !$this->isCurrent($days);
    }

    /**
     * Get days since rate was updated
     */
    public function getDaysSinceUpdate()
    {
        $now = time();
        $effective = strtotime($this->effective_date);
        $diff = $now - $effective;

        return max(0, floor($diff / 86400));
    }

    /**
     * Update rate value
     */
    public function updateRate($newRate, $effectiveDate = null)
    {
        if ($newRate <= 0) {
            throw new \InvalidArgumentException("Exchange rate must be positive");
        }

        $this->exchange_rate = $newRate;

        if ($effectiveDate) {
            $this->effective_date = $effectiveDate;
        }

        return $this->update(['exchange_rate', 'effective_date']);
    }

    /**
     * Validate currency rate
     */
    public function validateRate()
    {
        $errors = [];

        if (!preg_match('/^[A-Z]{3}$/', $this->from_currency)) {
            $errors[] = "From currency must be a 3-letter code (e.g., CAD)";
        }

        if (!preg_match('/^[A-Z]{3}$/', $this->to_currency)) {
            $errors[] = "To currency must be a 3-letter code (e.g., USD)";
        }

        if ($this->from_currency === $this->to_currency) {
            $errors[] = "From and to currencies must be different";
        }

        if ($this->exchange_rate <= 0) {
            $errors[] = "Exchange rate must be positive";
        }

        // Check for duplicate on same date
        $existing = static::new()
            ->where('from_currency', $this->from_currency)
            ->where('to_currency', $this->to_currency)
            ->where('effective_date', $this->effective_date)
            ->where('id', '!=', $this->id ?? 0)
            ->where('deleted_at', 'IS', null)
            ->first();

        if ($existing) {
            $errors[] = "Rate for {$this->from_currency} to {$this->to_currency} on {$this->effective_date} already exists";
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Get all unique currency codes used
     */
    public static function getAllCurrencies()
    {
        $wpdb = static::new()->getQuery()->getWpdb();
        $table = static::new()->getTable();

        $sql = "SELECT DISTINCT from_currency as currency FROM {$table} WHERE deleted_at IS NULL
            UNION
            SELECT DISTINCT to_currency as currency FROM {$table} WHERE deleted_at IS NULL
            ORDER BY currency";

        $results = $wpdb->get_results($sql);

        return array_column($results, 'currency');
    }

    /**
     * Get rate sources
     */
    public static function getAllSources()
    {
        return static::new()
            ->where('deleted_at', 'IS', null)
            ->orderBy('source', 'ASC')
            ->findAll()
            ->get();
    }

    /**
     * Soft delete (check if in use first)
     */
    public function softDelete()
    {
        // Check if this is the only rate available for this currency pair
        $count = static::new()
            ->where('from_currency', $this->from_currency)
            ->where('to_currency', $this->to_currency)
            ->where('deleted_at', 'IS', null)
            ->findAll()
            ->get();

        if (count($count) <= 1) {
            throw new \RuntimeException(
                "Cannot delete the only available rate for {$this->from_currency} to {$this->to_currency}"
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
}
