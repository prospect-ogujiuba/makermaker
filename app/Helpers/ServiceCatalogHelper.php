<?php

namespace MakerMaker\Helpers;

use MakerMaker\Models\Service;
use MakerMaker\Models\ServiceCategory;
use MakerMaker\Models\ServiceType;
use MakerMaker\Models\PricingModel;
use MakerMaker\Models\PricingTier;
use MakerMaker\Models\ServicePrice;
use MakerMaker\Models\PriceRecord;
use MakerMaker\Models\ComplexityLevel;
use MakerMaker\Models\CurrencyRate;
use MakerMaker\Models\Equipment;
use MakerMaker\Models\ServiceEquipment;
use MakerMaker\Models\CoverageArea;
use MakerMaker\Models\ServiceCoverage;
use MakerMaker\Models\Deliverable;
use MakerMaker\Models\ServiceDeliverable;
use MakerMaker\Models\DeliveryMethod;
use MakerMaker\Models\ServiceDelivery;
use MakerMaker\Models\ServiceRelationship;
use MakerMaker\Models\ServiceAddon;
use MakerMaker\Models\ServiceBundle;
use MakerMaker\Models\BundleItem;

/**
 * Service Catalog Helper Functions
 * 
 * Cross-model utility functions for Subset 1:
 * - Service, ServiceCategory, ServiceType, PricingModel, PricingTier
 */
class ServiceCatalogHelper
{
    /**
     * Format currency amount
     */
    public static function formatCurrency($amount, $currency = 'CAD', $decimals = 2)
    {
        $symbols = [
            'CAD' => '$',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AUD' => 'A$',
            'JPY' => '¥',
            'CHF' => 'CHF ',
            'MXN' => 'MX$'
        ];

        $symbol = $symbols[$currency] ?? $currency . ' ';

        // JPY doesn't use decimals
        if ($currency === 'JPY') {
            $decimals = 0;
        }

        return $symbol . number_format($amount, $decimals);
    }

    /**
     * Format hours as human-readable duration
     */
    public static function formatHours($hours, $includeMinutes = true)
    {
        if ($hours === null || $hours == 0) {
            return '0 hours';
        }

        if ($hours < 1 && $includeMinutes) {
            $minutes = round($hours * 60);
            return "{$minutes} minute" . ($minutes != 1 ? 's' : '');
        }

        $fullHours = floor($hours);
        $remainingMinutes = round(($hours - $fullHours) * 60);

        $result = "{$fullHours} hour" . ($fullHours != 1 ? 's' : '');

        if ($remainingMinutes > 0 && $includeMinutes) {
            $result .= " {$remainingMinutes} minute" . ($remainingMinutes != 1 ? 's' : '');
        }

        return $result;
    }

    /**
     * Format hours as work days (8-hour days)
     */
    public static function formatHoursAsDays($hours, $hoursPerDay = 8)
    {
        if ($hours === null || $hours == 0) {
            return '0 days';
        }

        if ($hours < $hoursPerDay) {
            return self::formatHours($hours);
        }

        $days = floor($hours / $hoursPerDay);
        $remainingHours = $hours % $hoursPerDay;

        $result = "{$days} day" . ($days != 1 ? 's' : '');

        if ($remainingHours > 0) {
            $result .= ", " . self::formatHours($remainingHours, false);
        }

        return $result;
    }

    /**
     * Calculate price with complexity multiplier
     */
    public static function calculateComplexityAdjustedPrice($basePrice, $complexityMultiplier)
    {
        return $basePrice * $complexityMultiplier;
    }

    /**
     * Calculate price with tier discount
     */
    public static function calculateTierDiscountedPrice($basePrice, $discountPct)
    {
        return $basePrice * (1 - ($discountPct / 100));
    }

    /**
     * Calculate final service price (with both complexity and discount)
     */
    public static function calculateFinalPrice($basePrice, $complexityMultiplier, $discountPct, $setupFee = 0)
    {
        $adjustedPrice = self::calculateComplexityAdjustedPrice($basePrice, $complexityMultiplier);
        $discountedPrice = self::calculateTierDiscountedPrice($adjustedPrice, $discountPct);

        return [
            'base_price' => $basePrice,
            'complexity_adjusted' => $adjustedPrice,
            'after_discount' => $discountedPrice,
            'setup_fee' => $setupFee,
            'total' => $discountedPrice + $setupFee,
            'total_savings' => $adjustedPrice - $discountedPrice
        ];
    }

    /**
     * Get active service count by category
     */
    public static function getServiceCountByCategory()
    {
        $categories = ServiceCategory::new()->getActive()->get();
        $counts = [];

        if ($categories) {
            foreach ($categories as $category) {
                $counts[$category->id] = [
                    'category' => $category->name,
                    'count' => $category->getServiceCount(true)
                ];
            }
        }

        return $counts;
    }

    /**
     * Get active service count by type
     */
    public static function getServiceCountByType()
    {
        $types = ServiceType::new()->getActive()->get();
        $counts = [];

        if ($types) {
            foreach ($types as $type) {
                $counts[$type->id] = [
                    'type' => $type->name,
                    'count' => $type->getServiceCount(true)
                ];
            }
        }

        return $counts;
    }

    /**
     * Build hierarchical category tree for dropdowns
     */
    public static function getCategorySelectOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Category —';
        }

        $flatTree = ServiceCategory::getFlatTree();

        if ($flatTree) {
            foreach ($flatTree as $item) {
                $options[$item['id']] = $item['name'];
            }
        }

        return $options;
    }

    /**
     * Build service type select options
     */
    public static function getServiceTypeSelectOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Service Type —';
        }

        $types = ServiceType::new()->getActive()->get();

        if ($types) {
            foreach ($types as $type) {
                $options[$type->id] = $type->name;
            }
        }

        return $options;
    }

    /**
     * Build pricing model select options
     */
    public static function getPricingModelSelectOptions($includeEmpty = true, $timeBasedOnly = null)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Pricing Model —';
        }

        $query = PricingModel::new();

        if ($timeBasedOnly !== null) {
            $models = $timeBasedOnly ? $query->getTimeBased()->get() : $query->getFixedBased()->get();
        } else {
            $models = $query->getActive()->get();
        }

        if ($models) {
            foreach ($models as $model) {
                $options[$model->id] = $model->getDisplayLabel();
            }
        }

        return $options;
    }

    /**
     * Build pricing tier select options
     */
    public static function getPricingTierSelectOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Pricing Tier —';
        }

        $tiers = PricingTier::new()->getActive()->get();

        if ($tiers) {
            foreach ($tiers as $tier) {
                $options[$tier->id] = $tier->getDisplayLabel();
            }
        }

        return $options;
    }

    /**
     * Validate SKU format
     */
    public static function validateSKU($sku)
    {
        // SKU should be uppercase alphanumeric with hyphens
        if (!preg_match('/^[A-Z0-9\-]+$/', $sku)) {
            return "SKU must contain only uppercase letters, numbers, and hyphens";
        }

        if (strlen($sku) > 64) {
            return "SKU must be 64 characters or less";
        }

        return true;
    }

    /**
     * Generate SKU from service name
     */
    public static function generateSKU($serviceName, $prefix = '')
    {
        // Remove special characters and convert to uppercase
        $sku = strtoupper(preg_replace('/[^A-Za-z0-9\-]/', '-', $serviceName));

        // Remove multiple consecutive hyphens
        $sku = preg_replace('/-+/', '-', $sku);

        // Trim hyphens from start and end
        $sku = trim($sku, '-');

        // Add prefix if provided
        if ($prefix) {
            $sku = strtoupper($prefix) . '-' . $sku;
        }

        // Truncate to 64 characters
        if (strlen($sku) > 64) {
            $sku = substr($sku, 0, 64);
        }

        return $sku;
    }

    /**
     * Validate slug format
     */
    public static function validateSlug($slug)
    {
        // Slug should be lowercase alphanumeric with hyphens
        if (!preg_match('/^[a-z0-9\-]+$/', $slug)) {
            return "Slug must contain only lowercase letters, numbers, and hyphens";
        }

        if (strlen($slug) > 64) {
            return "Slug must be 64 characters or less";
        }

        return true;
    }

    /**
     * Generate slug from string
     */
    public static function generateSlug($string)
    {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(str_replace(' ', '-', $string));

        // Remove special characters
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);

        // Remove multiple consecutive hyphens
        $slug = preg_replace('/-+/', '-', $slug);

        // Trim hyphens from start and end
        $slug = trim($slug, '-');

        // Truncate to 64 characters
        if (strlen($slug) > 64) {
            $slug = substr($slug, 0, 64);
        }

        return $slug;
    }

    /**
     * Check if SKU is unique
     */
    public static function isSKUUnique($sku, $excludeServiceId = null)
    {
        $query = Service::new()->where('sku', $sku);

        if ($excludeServiceId) {
            $query->where('id', '!=', $excludeServiceId);
        }

        $existing = $query->first();

        return $existing === null;
    }

    /**
     * Check if slug is unique
     */
    public static function isSlugUnique($slug, $excludeServiceId = null)
    {
        $query = Service::new()->where('slug', $slug);

        if ($excludeServiceId) {
            $query->where('id', '!=', $excludeServiceId);
        }

        $existing = $query->first();

        return $existing === null;
    }

    /**
     * Get skill level label with icon/badge
     */
    public static function getSkillLevelBadge($skillLevel)
    {
        $badges = [
            'entry' => '<span class="skill-badge entry">Entry Level</span>',
            'intermediate' => '<span class="skill-badge intermediate">Intermediate</span>',
            'advanced' => '<span class="skill-badge advanced">Advanced</span>',
            'expert' => '<span class="skill-badge expert">Expert</span>',
            'specialist' => '<span class="skill-badge specialist">Specialist</span>',
        ];

        return $badges[$skillLevel] ?? '<span class="skill-badge unknown">Unknown</span>';
    }

    /**
     * Get skill level numeric value for sorting/comparison
     */
    public static function getSkillLevelValue($skillLevel)
    {
        $values = [
            'entry' => 1,
            'intermediate' => 2,
            'advanced' => 3,
            'expert' => 4,
            'specialist' => 5
        ];

        return $values[$skillLevel] ?? 0;
    }

    /**
     * Compare two skill levels
     * Returns: -1 if level1 < level2, 0 if equal, 1 if level1 > level2
     */
    public static function compareSkillLevels($level1, $level2)
    {
        $value1 = self::getSkillLevelValue($level1);
        $value2 = self::getSkillLevelValue($level2);

        if ($value1 < $value2) return -1;
        if ($value1 > $value2) return 1;
        return 0;
    }

    /**
     * Get all valid skill levels
     */
    public static function getSkillLevels()
    {
        return [
            'entry' => 'Entry Level',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced',
            'expert' => 'Expert',
            'specialist' => 'Specialist'
        ];
    }

    /**
     * Build breadcrumb trail for a category
     */
    public static function getCategoryBreadcrumbs($categoryId)
    {
        $category = ServiceCategory::new()->findById($categoryId);

        if (!$category) {
            return [];
        }

        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug,
                'url' => '/services/category/' . $current->slug
            ]);

            $current = $current->parent_id ? $current->parentCategory : null;
        }

        return $breadcrumbs;
    }

    /**
     * Get service status badge/label
     */
    public static function getServiceStatusBadge($isActive, $deletedAt = null)
    {
        if ($deletedAt !== null) {
            return '<span class="status-badge deleted">Deleted</span>';
        }

        if ($isActive) {
            return '<span class="status-badge active">Active</span>';
        }

        return '<span class="status-badge inactive">Inactive</span>';
    }

    /**
     * Sanitize and prepare metadata array for saving
     */
    public static function sanitizeMetadata($metadata)
    {
        if (!is_array($metadata)) {
            return [];
        }

        // Remove empty values
        $metadata = array_filter($metadata, function ($value) {
            return $value !== null && $value !== '';
        });

        // Ensure all keys are strings
        $sanitized = [];
        foreach ($metadata as $key => $value) {
            $sanitized[sanitize_key($key)] = $value;
        }

        return $sanitized;
    }

    /**
     * Calculate total estimated project cost
     */
    public static function calculateProjectCost($serviceId, $quantity, $tierCode = 'small_business', $currency = 'CAD')
    {
        $service = Service::new()->findById($serviceId);

        if (!$service) {
            return null;
        }

        $priceData = $service->getCalculatedPrice($serviceId, $currency, $tierCode);

        if (!$priceData) {
            return null;
        }

        $lineTotal = $priceData['adjusted_amount'] * $quantity;
        $setupFee = $priceData['setup_fee'] ?? 0;

        return [
            'service_name' => $service->name,
            'quantity' => $quantity,
            'unit_price' => $priceData['base_amount'],
            'complexity_multiplier' => $priceData['multiplier'],
            'adjusted_unit_price' => $priceData['adjusted_amount'],
            'line_subtotal' => $lineTotal,
            'setup_fee' => $setupFee,
            'total' => $lineTotal + $setupFee,
            'currency' => $currency,
            'unit' => $priceData['unit']
        ];
    }

    /**
     * Calculate multiple services total (shopping cart)
     */
    public static function calculateCartTotal($items, $tierCode = 'small_business', $currency = 'CAD')
    {
        // $items format: [['service_id' => 1, 'quantity' => 2], ...]

        $lineItems = [];
        $subtotal = 0;
        $totalSetupFees = 0;

        foreach ($items as $item) {
            $cost = self::calculateProjectCost(
                $item['service_id'],
                $item['quantity'],
                $tierCode,
                $currency
            );

            if ($cost) {
                $lineItems[] = $cost;
                $subtotal += $cost['line_subtotal'];
                $totalSetupFees += $cost['setup_fee'];
            }
        }

        return [
            'line_items' => $lineItems,
            'subtotal' => $subtotal,
            'total_setup_fees' => $totalSetupFees,
            'grand_total' => $subtotal + $totalSetupFees,
            'currency' => $currency,
            'item_count' => count($lineItems)
        ];
    }

    /**
     * Get services filtered by multiple criteria
     */
    public static function filterServices($filters = [])
    {
        $query = Service::new()
            ->where('is_active', 1)
            ->where('deleted_at', 'IS', null);

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['service_type_id'])) {
            $query->where('service_type_id', $filters['service_type_id']);
        }

        if (!empty($filters['complexity_id'])) {
            $query->where('complexity_id', $filters['complexity_id']);
        }

        if (!empty($filters['skill_level'])) {
            $query->where('skill_level', $filters['skill_level']);
        }

        if (!empty($filters['is_featured'])) {
            $query->where('is_featured', 1);
        }

        if (!empty($filters['keyword'])) {
            $keyword = $filters['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('short_desc', 'LIKE', "%{$keyword}%")
                    ->orWhere('long_desc', 'LIKE', "%{$keyword}%");
            });
        }

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortDir = $filters['sort_dir'] ?? 'ASC';

        $query->orderBy($sortBy, $sortDir);

        return $query->findAll();
    }

    /**
     * Truncate description with ellipsis
     */
    public static function truncateDescription($text, $maxLength = 150, $ellipsis = '...')
    {
        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = substr($text, 0, $maxLength);

        // Try to cut at last complete word
        $lastSpace = strrpos($truncated, ' ');
        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . $ellipsis;
    }

    /**
     * Convert empty string to null (for nullable fields)
     */
    public static function convertEmptyToNull($value)
    {
        return ($value === '' || $value === null) ? null : $value;
    }

    /**
     * Get currency symbol
     */
    public static function getCurrencySymbol($currency)
    {
        $symbols = [
            'CAD' => '$',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AUD' => 'A$',
            'JPY' => '¥',
            'CHF' => 'CHF ',
            'MXN' => 'MX$'
        ];

        return $symbols[$currency] ?? $currency;
    }

    /**
     * Get all supported currencies
     */
    public static function getSupportedCurrencies()
    {
        return [
            'CAD' => 'Canadian Dollar (CAD)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
            'GBP' => 'British Pound (GBP)',
            'AUD' => 'Australian Dollar (AUD)',
            'JPY' => 'Japanese Yen (JPY)',
            'CHF' => 'Swiss Franc (CHF)',
            'MXN' => 'Mexican Peso (MXN)'
        ];
    }

    // ========================================================================
    // SUBSET 2 HELPERS - Pricing, History, Complexity, Currency
    // ========================================================================

    /**
     * Get approval status select options
     */
    public static function getApprovalStatusOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Status —';
        }

        $options['draft'] = 'Draft';
        $options['pending'] = 'Pending Approval';
        $options['approved'] = 'Approved';
        $options['rejected'] = 'Rejected';

        return $options;
    }

    /**
     * Get approval status badge HTML
     */
    public static function getApprovalStatusBadge($status)
    {
        $badges = [
            'draft' => '<span class="status-badge draft">Draft</span>',
            'pending' => '<span class="status-badge pending">Pending</span>',
            'approved' => '<span class="status-badge approved">✓ Approved</span>',
            'rejected' => '<span class="status-badge rejected">✗ Rejected</span>',
        ];

        return $badges[$status] ?? '<span class="status-badge unknown">Unknown</span>';
    }

    /**
     * Calculate price with all adjustments (complexity + tier discount)
     */
    public static function calculateFullyAdjustedPrice($basePrice, $complexityMultiplier, $tierDiscountPct, $setupFee = 0)
    {
        $complexityAdjusted = $basePrice * $complexityMultiplier;
        $tierDiscounted = $complexityAdjusted * (1 - ($tierDiscountPct / 100));

        return [
            'base_price' => $basePrice,
            'complexity_multiplier' => $complexityMultiplier,
            'after_complexity' => $complexityAdjusted,
            'tier_discount_pct' => $tierDiscountPct,
            'discount_amount' => $complexityAdjusted - $tierDiscounted,
            'after_discount' => $tierDiscounted,
            'setup_fee' => $setupFee,
            'grand_total' => $tierDiscounted + $setupFee
        ];
    }

    /**
     * Format price change with direction indicator
     */
    public static function formatPriceChange($oldPrice, $newPrice, $currency = 'CAD')
    {
        $change = $newPrice - $oldPrice;
        $changePercent = $oldPrice > 0 ? round(($change / $oldPrice) * 100, 2) : 0;

        $symbol = self::getCurrencySymbol($currency);
        $formattedChange = $symbol . number_format(abs($change), 2);

        if ($change > 0) {
            return "↑ +{$formattedChange} (+{$changePercent}%)";
        } elseif ($change < 0) {
            return "↓ -{$formattedChange} ({$changePercent}%)";
        } else {
            return "No change";
        }
    }

    /**
     * Get complexity level select options
     */
    public static function getComplexityLevelOptions($includeEmpty = true)
    {
        return ComplexityLevel::getSelectOptions($includeEmpty);
    }

    /**
     * Convert between currencies using latest rate
     */
    public static function convertCurrency($amount, $fromCurrency, $toCurrency, $date = null)
    {
        return CurrencyRate::convert($amount, $fromCurrency, $toCurrency, $date);
    }

    /**
     * Get exchange rate display
     */
    public static function getExchangeRateDisplay($fromCurrency, $toCurrency, $date = null)
    {
        $rate = CurrencyRate::getRateForDate($fromCurrency, $toCurrency, $date);

        if (!$rate) {
            return "Rate not available";
        }

        return sprintf(
            '1 %s = %s %s',
            $fromCurrency,
            number_format($rate->exchange_rate, 4),
            $toCurrency
        );
    }

    /**
     * Validate currency code format
     */
    public static function isValidCurrency($currency)
    {
        return preg_match('/^[A-Z]{3}$/', $currency) === 1;
    }

    /**
     * Get price validity status
     */
    public static function getPriceValidityStatus($validFrom, $validTo)
    {
        $now = date('Y-m-d H:i:s');

        if ($validFrom > $now) {
            return [
                'status' => 'future',
                'label' => 'Starts ' . date('M j, Y', strtotime($validFrom)),
                'class' => 'status-future'
            ];
        }

        if ($validTo && $validTo < $now) {
            return [
                'status' => 'expired',
                'label' => 'Expired ' . date('M j, Y', strtotime($validTo)),
                'class' => 'status-expired'
            ];
        }

        if ($validTo) {
            $daysUntilExpiry = ceil((strtotime($validTo) - time()) / 86400);

            if ($daysUntilExpiry <= 7) {
                return [
                    'status' => 'expiring_soon',
                    'label' => "Expires in {$daysUntilExpiry} day" . ($daysUntilExpiry != 1 ? 's' : ''),
                    'class' => 'status-expiring-soon'
                ];
            }

            return [
                'status' => 'active',
                'label' => 'Active until ' . date('M j, Y', strtotime($validTo)),
                'class' => 'status-active'
            ];
        }

        return [
            'status' => 'active',
            'label' => 'Active (no end date)',
            'class' => 'status-active'
        ];
    }

    /**
     * Calculate price range for a service across all tiers
     */
    public static function getServicePriceRange($serviceId, $currency = 'CAD', $includeComplexity = true)
    {
        $service = Service::new()->findById($serviceId);

        if (!$service) {
            return null;
        }

        $prices = $service->prices;
        $amounts = [];

        if ($prices) {
            foreach ($prices as $price) {
                if (
                    $price->currency === $currency &&
                    $price->is_current == 1 &&
                    $price->approval_status === 'approved'
                ) {

                    $amount = $price->amount;

                    if ($includeComplexity && $service->complexity) {
                        $amount *= $service->complexity->price_multiplier;
                    }

                    $amounts[] = $amount;
                }
            }
        }

        if (empty($amounts)) {
            return null;
        }

        $min = min($amounts);
        $max = max($amounts);

        return [
            'min' => $min,
            'max' => $max,
            'currency' => $currency,
            'formatted' => $min === $max
                ? self::formatCurrency($min, $currency)
                : self::formatCurrency($min, $currency) . ' - ' . self::formatCurrency($max, $currency)
        ];
    }

    /**
     * Get price change summary for history display
     */
    public static function getPriceChangeSummary($priceRecord)
    {
        $summary = [];

        if ($priceRecord->old_amount !== null && $priceRecord->new_amount !== null) {
            $change = $priceRecord->new_amount - $priceRecord->old_amount;
            $currency = $priceRecord->new_currency ?? $priceRecord->old_currency ?? 'CAD';

            if ($change != 0) {
                $summary[] = [
                    'field' => 'Amount',
                    'change' => self::formatPriceChange($priceRecord->old_amount, $priceRecord->new_amount, $currency)
                ];
            }
        }

        if ($priceRecord->old_currency !== $priceRecord->new_currency && $priceRecord->new_currency) {
            $summary[] = [
                'field' => 'Currency',
                'change' => "{$priceRecord->old_currency} → {$priceRecord->new_currency}"
            ];
        }

        if ($priceRecord->old_unit !== $priceRecord->new_unit && $priceRecord->new_unit) {
            $summary[] = [
                'field' => 'Unit',
                'change' => "{$priceRecord->old_unit} → {$priceRecord->new_unit}"
            ];
        }

        if ($priceRecord->old_approval_status !== $priceRecord->new_approval_status && $priceRecord->new_approval_status) {
            $summary[] = [
                'field' => 'Status',
                'change' => ucfirst($priceRecord->old_approval_status) . ' → ' . ucfirst($priceRecord->new_approval_status)
            ];
        }

        return $summary;
    }

    /**
     * Compare two service prices
     */
    public static function comparePrices($price1, $price2, $convertCurrency = true)
    {
        $amount1 = $price1->amount;
        $amount2 = $price2->amount;

        // Convert to same currency if needed
        if ($convertCurrency && $price1->currency !== $price2->currency) {
            $amount2 = self::convertCurrency($amount2, $price2->currency, $price1->currency);
        }

        $difference = $amount2 - $amount1;
        $percentChange = $amount1 > 0 ? round(($difference / $amount1) * 100, 2) : 0;

        return [
            'price1_amount' => $amount1,
            'price1_currency' => $price1->currency,
            'price2_amount' => $price2->amount,
            'price2_currency' => $price2->currency,
            'converted_amount2' => $amount2,
            'difference' => $difference,
            'percent_change' => $percentChange,
            'direction' => $difference > 0 ? 'increase' : ($difference < 0 ? 'decrease' : 'no_change')
        ];
    }

    /**
     * Get pricing units commonly used
     */
    public static function getPricingUnits()
    {
        return [
            // Time-based
            'hour' => 'Per Hour',
            'day' => 'Per Day',
            'week' => 'Per Week',
            'month' => 'Per Month',
            'year' => 'Per Year',

            // Quantity-based
            'each' => 'Per Unit/Each',
            'device' => 'Per Device',
            'user' => 'Per User',
            'license' => 'Per License',
            'site' => 'Per Site',
            'location' => 'Per Location',

            // Area-based
            'sqft' => 'Per Square Foot',
            'sqm' => 'Per Square Meter',

            // Volume-based
            'gb' => 'Per GB',
            'tb' => 'Per TB',

            // Project-based
            'project' => 'Per Project',
            'fixed' => 'Fixed Price'
        ];
    }

    /**
     * Validate pricing unit
     */
    public static function isValidUnit($unit)
    {
        $units = self::getPricingUnits();
        return array_key_exists($unit, $units);
    }

    /**
     * Get unit display label
     */
    public static function getUnitLabel($unit)
    {
        $units = self::getPricingUnits();
        return $units[$unit] ?? $unit;
    }

    /**
     * Build complexity comparison table data
     */
    public static function getComplexityComparison($basePrice, $currency = 'CAD')
    {
        $levels = ComplexityLevel::new()->getActive()->get();

        if (!$levels) {
            return [];
        }

        $comparison = [];

        foreach ($levels as $level) {
            $adjustedPrice = $basePrice * $level->price_multiplier;
            $priceImpact = $adjustedPrice - $basePrice;
            $percentImpact = $basePrice > 0 ? round(($priceImpact / $basePrice) * 100, 1) : 0;

            $comparison[] = [
                'id' => $level->id,
                'level' => $level->level,
                'name' => $level->name,
                'multiplier' => $level->price_multiplier,
                'base_price' => $basePrice,
                'adjusted_price' => $adjustedPrice,
                'price_impact' => $priceImpact,
                'percent_impact' => $percentImpact,
                'formatted_base' => self::formatCurrency($basePrice, $currency),
                'formatted_adjusted' => self::formatCurrency($adjustedPrice, $currency),
                'formatted_impact' => self::formatPriceChange($basePrice, $adjustedPrice, $currency)
            ];
        }

        return $comparison;
    }

    /**
     * Calculate multi-currency price summary
     */
    public static function getMultiCurrencyPricing($baseAmount, $baseCurrency, $targetCurrencies, $date = null)
    {
        $result = [
            'base' => [
                'currency' => $baseCurrency,
                'amount' => $baseAmount,
                'formatted' => self::formatCurrency($baseAmount, $baseCurrency)
            ],
            'conversions' => []
        ];

        foreach ($targetCurrencies as $targetCurrency) {
            if ($targetCurrency === $baseCurrency) {
                continue;
            }

            try {
                $convertedAmount = self::convertCurrency($baseAmount, $baseCurrency, $targetCurrency, $date);
                $rate = CurrencyRate::getRateForDate($baseCurrency, $targetCurrency, $date);

                $result['conversions'][$targetCurrency] = [
                    'currency' => $targetCurrency,
                    'amount' => $convertedAmount,
                    'formatted' => self::formatCurrency($convertedAmount, $targetCurrency),
                    'exchange_rate' => $rate ? $rate->exchange_rate : null,
                    'rate_date' => $rate ? $rate->effective_date : null
                ];
            } catch (\Exception $e) {
                $result['conversions'][$targetCurrency] = [
                    'currency' => $targetCurrency,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $result;
    }

    /**
     * Get price audit trail summary
     */
    public static function getPriceAuditSummary($servicePriceId, $limit = 10)
    {
        $records = PriceRecord::getHistoryFor($servicePriceId, $limit)->get();

        if (!$records) {
            return [];
        }

        $summary = [];

        foreach ($records as $record) {
            $summary[] = [
                'id' => $record->id,
                'change_type' => $record->change_type,
                'icon' => $record->getChangeIcon(),
                'summary' => $record->getChangeSummary(),
                'amount_change' => $record->getAmountChange(),
                'amount_change_percent' => $record->getAmountChangePercent(),
                'changed_by' => $record->changedBy->display_name ?? 'Unknown',
                'changed_at' => $record->changed_at,
                'formatted_date' => $record->getFormattedDate(),
                'time_ago' => $record->getTimeAgo(),
                'details' => self::getPriceChangeSummary($record)
            ];
        }

        return $summary;
    }

    /**
     * Calculate effective price on a specific date
     */
    public static function getEffectivePriceOnDate($serviceId, $tierId, $modelId, $date, $currency = 'CAD')
    {
        $prices = ServicePrice::new()->getValidOnDate($date, $serviceId)->get();

        if (!$prices) {
            return null;
        }

        foreach ($prices as $price) {
            if (
                $price->pricing_tier_id == $tierId &&
                $price->pricing_model_id == $modelId &&
                $price->currency === $currency &&
                $price->approval_status === 'approved'
            ) {

                return $price;
            }
        }

        return null;
    }

    /**
     * Bulk convert prices to different currency
     */
    public static function bulkConvertPrices($prices, $targetCurrency, $date = null)
    {
        $converted = [];

        foreach ($prices as $price) {
            try {
                $convertedAmount = self::convertCurrency(
                    $price->amount,
                    $price->currency,
                    $targetCurrency,
                    $date
                );

                $converted[] = [
                    'original_price' => $price,
                    'converted_amount' => $convertedAmount,
                    'target_currency' => $targetCurrency,
                    'formatted' => self::formatCurrency($convertedAmount, $targetCurrency)
                ];
            } catch (\Exception $e) {
                $converted[] = [
                    'original_price' => $price,
                    'error' => $e->getMessage()
                ];
            }
        }

        return $converted;
    }

    // ========================================================================
    // SUBSET 3A HELPERS - Equipment & Coverage
    // Add these functions to the ServiceCatalogHelper class
    // ========================================================================

    /**
     * Get equipment category select options
     */
    public static function getEquipmentCategoryOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Category —';
        }

        $categories = Equipment::getAllCategories();

        foreach ($categories as $category) {
            $options[$category] = $category;
        }

        return $options;
    }

    /**
     * Get manufacturer select options
     */
    public static function getManufacturerOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Manufacturer —';
        }

        $manufacturers = Equipment::getAllManufacturers();

        foreach ($manufacturers as $manufacturer) {
            $options[$manufacturer] = $manufacturer;
        }

        return $options;
    }

    /**
     * Calculate total equipment cost for service
     */
    public static function calculateServiceEquipmentCost($serviceId, $includeOptional = false, $onlyAdditional = false)
    {
        $items = ServiceEquipment::getForService($serviceId)->get();

        if (!$items) {
            return 0;
        }

        $total = 0;

        foreach ($items as $item) {
            // Skip optional if not included
            if (!$includeOptional && !$item->required) {
                continue;
            }

            // Skip included costs if only counting additional
            if ($onlyAdditional && $item->cost_included) {
                continue;
            }

            $total += $item->getTotalCost();
        }

        return $total;
    }

    /**
     * Get equipment cost breakdown for service
     */
    public static function getEquipmentCostBreakdown($serviceId, $currency = 'CAD')
    {
        $costs = ServiceEquipment::getAllCostsForService($serviceId);

        return [
            'total_cost' => $costs['total_cost'],
            'required_cost' => $costs['required_cost'],
            'optional_cost' => $costs['optional_cost'],
            'included_cost' => $costs['included_cost'],
            'additional_cost' => $costs['additional_cost'],
            'formatted_total' => self::formatCurrency($costs['total_cost'], $currency),
            'formatted_required' => self::formatCurrency($costs['required_cost'], $currency),
            'formatted_optional' => self::formatCurrency($costs['optional_cost'], $currency),
            'formatted_included' => self::formatCurrency($costs['included_cost'], $currency),
            'formatted_additional' => self::formatCurrency($costs['additional_cost'], $currency)
        ];
    }

    /**
     * Check if service is available in coverage area
     */
    public static function isServiceAvailableInArea($serviceId, $coverageAreaId)
    {
        return ServiceCoverage::isServiceAvailableInArea($serviceId, $coverageAreaId);
    }

    /**
     * Calculate delivery cost with surcharge
     */
    public static function calculateDeliveryCost($basePrice, $serviceId, $coverageAreaId)
    {
        $coverage = ServiceCoverage::new()
            ->where('service_id', $serviceId)
            ->where('coverage_area_id', $coverageAreaId)
            ->where('deleted_at', 'IS', null)
            ->first();

        if (!$coverage) {
            return [
                'available' => false,
                'base_price' => $basePrice,
                'surcharge' => 0,
                'total' => $basePrice
            ];
        }

        return [
            'available' => true,
            'base_price' => $basePrice,
            'surcharge' => $coverage->delivery_surcharge,
            'total' => $basePrice + $coverage->delivery_surcharge,
            'lead_time_adjustment' => $coverage->lead_time_adjustment_days
        ];
    }

    /**
     * Get coverage area select options
     */
    public static function getCoverageAreaOptions($includeEmpty = true, $groupByCountry = false)
    {
        return CoverageArea::getSelectOptions($includeEmpty, $groupByCountry);
    }

    /**
     * Get region type select options
     */
    public static function getRegionTypeOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Region Type —';
        }

        $options['city'] = 'City';
        $options['province'] = 'Province';
        $options['state'] = 'State';
        $options['region'] = 'Region';
        $options['postal_code'] = 'Postal Code Area';
        $options['country'] = 'Country';

        return $options;
    }

    /**
     * Format equipment list for display
     */
    public static function formatEquipmentList($equipmentItems, $showCosts = true, $currency = 'CAD')
    {
        $formatted = [];

        foreach ($equipmentItems as $item) {
            $equipment = $item->equipment;

            if (!$equipment) {
                continue;
            }

            $line = [
                'name' => $equipment->name,
                'quantity' => $item->getFormattedQuantity(),
                'required' => $item->isRequired(),
                'cost_included' => $item->isCostIncluded()
            ];

            if ($showCosts) {
                $line['unit_cost'] = $equipment->unit_cost;
                $line['total_cost'] = $item->getTotalCost();
                $line['formatted_cost'] = $item->getFormattedTotalCost($currency);
            }

            $formatted[] = $line;
        }

        return $formatted;
    }

    /**
     * Get quantity unit options
     */
    public static function getQuantityUnitOptions()
    {
        return [
            'each' => 'Each',
            'box' => 'Box',
            'case' => 'Case',
            'pack' => 'Pack',
            'roll' => 'Roll',
            'feet' => 'Feet',
            'meter' => 'Meter',
            'kg' => 'Kilogram',
            'lb' => 'Pound',
            'liter' => 'Liter',
            'gallon' => 'Gallon'
        ];
    }

    /**
     * Calculate service availability matrix
     */
    public static function getServiceAvailabilityMatrix($serviceIds, $coverageAreaIds)
    {
        $matrix = [];

        foreach ($serviceIds as $serviceId) {
            $matrix[$serviceId] = [];

            foreach ($coverageAreaIds as $areaId) {
                $coverage = ServiceCoverage::new()
                    ->where('service_id', $serviceId)
                    ->where('coverage_area_id', $areaId)
                    ->where('deleted_at', 'IS', null)
                    ->first();

                $matrix[$serviceId][$areaId] = [
                    'available' => $coverage !== null,
                    'surcharge' => $coverage ? $coverage->delivery_surcharge : null,
                    'lead_adjustment' => $coverage ? $coverage->lead_time_adjustment_days : null
                ];
            }
        }

        return $matrix;
    }

    /**
     * Get equipment type badge
     */
    public static function getEquipmentTypeBadge($isConsumable)
    {
        if ($isConsumable) {
            return '<span class="equipment-type-badge consumable">Consumable</span>';
        }

        return '<span class="equipment-type-badge durable">Durable</span>';
    }

    /**
     * Validate postal code against coverage area pattern
     */
    public static function validatePostalCodeForArea($postalCode, $coverageAreaId)
    {
        $area = CoverageArea::new()->findById($coverageAreaId);

        if (!$area) {
            return false;
        }

        return $area->matchesPostalCode($postalCode);
    }

    /**
     * Find coverage area by postal code
     */
    public static function findCoverageAreaByPostalCode($postalCode)
    {
        $areas = CoverageArea::new()->getActive()->get();

        if (!$areas) {
            return null;
        }

        foreach ($areas as $area) {
            if ($area->matchesPostalCode($postalCode)) {
                return $area;
            }
        }

        return null;
    }

    /**
     * Get complete service quote with equipment and delivery
     */
    public static function getCompleteServiceQuote($serviceId, $coverageAreaId, $quantity = 1, $currency = 'CAD', $tierCode = 'small_business')
    {
        $service = Service::new()->findById($serviceId);

        if (!$service) {
            return null;
        }

        // Get base price
        $priceData = $service->getCalculatedPrice($serviceId, $currency, $tierCode);

        if (!$priceData) {
            return null;
        }

        // Get equipment costs
        $equipmentCosts = self::getEquipmentCostBreakdown($serviceId, $currency);

        // Get delivery costs
        $deliveryCosts = self::calculateDeliveryCost($priceData['adjusted_amount'], $serviceId, $coverageAreaId);

        $subtotal = ($priceData['adjusted_amount'] * $quantity) + $equipmentCosts['additional_cost'];
        $total = $subtotal + $priceData['setup_fee'] + $deliveryCosts['surcharge'];

        return [
            'service' => $service,
            'quantity' => $quantity,
            'unit_price' => $priceData['adjusted_amount'],
            'line_total' => $priceData['adjusted_amount'] * $quantity,
            'setup_fee' => $priceData['setup_fee'],
            'equipment_included' => $equipmentCosts['included_cost'],
            'equipment_additional' => $equipmentCosts['additional_cost'],
            'delivery_surcharge' => $deliveryCosts['surcharge'],
            'lead_time_adjustment' => $deliveryCosts['lead_time_adjustment'] ?? 0,
            'subtotal' => $subtotal,
            'total' => $total,
            'currency' => $currency,
            'available_in_area' => $deliveryCosts['available']
        ];
    }

    // ========================================================================
    // SUBSET - Deliverable, ServiceDelivery, DeliveryMethod, ServiceDelivery
    // ========================================================================

    /**
     * Get all deliverables for a service with their details
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getServiceDeliverables($serviceId)
    {
        if (empty($serviceId)) {
            return [];
        }

        $serviceDeliverables = ServiceDeliverable::getByService($serviceId);

        if ($serviceDeliverables->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($serviceDeliverables as $sd) {
            $deliverable = $sd->getDeliverable();

            if ($deliverable) {
                $result[] = [
                    'id' => $deliverable->getID(),
                    'name' => $deliverable->name ?? '',
                    'type' => $deliverable->type ?? '',
                    'is_required' => $sd->isRequired(),
                    'quantity' => $sd->getQuantity(),
                    'formatted_quantity' => $sd->getFormattedQuantity(),
                    'delivery_days' => $sd->getDeliveryDays(),
                    'formatted_delivery_time' => $sd->getFormattedDeliveryTime(),
                ];
            }
        }

        return $result;
    }

    /**
     * Get all delivery methods for a service with their details
     * 
     * @param int $serviceId
     * @param bool $availableOnly
     * @return array
     */
    public static function getServiceDeliveryMethods($serviceId, $availableOnly = true)
    {
        if (empty($serviceId)) {
            return [];
        }

        $serviceDeliveries = ServiceDelivery::getByService($serviceId);

        if ($serviceDeliveries->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($serviceDeliveries as $sd) {
            if ($availableOnly && !$sd->isAvailable()) {
                continue;
            }

            $deliveryMethod = $sd->getDeliveryMethod();

            if ($deliveryMethod) {
                $result[] = [
                    'id' => $deliveryMethod->getID(),
                    'name' => $deliveryMethod->name ?? '',
                    'type' => $deliveryMethod->type ?? '',
                    'is_default' => $sd->isDefault(),
                    'additional_cost' => $sd->getAdditionalCost(),
                    'formatted_additional_cost' => $sd->getFormattedAdditionalCost(),
                    'total_cost' => $sd->getTotalCost(),
                    'formatted_total_cost' => $sd->getFormattedTotalCost(),
                    'estimated_days' => $sd->getEstimatedDays(),
                    'formatted_estimate' => $sd->getFormattedEstimate(),
                    'priority' => $sd->getPriority(),
                ];
            }
        }

        // Sort by priority
        usort($result, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $result;
    }

    /**
     * Get default delivery method for a service
     * 
     * @param int $serviceId
     * @return array|null
     */
    public static function getDefaultDeliveryMethod($serviceId)
    {
        if (empty($serviceId)) {
            return null;
        }

        $serviceDelivery = ServiceDelivery::new()
            ->where('service_id', $serviceId)
            ->where('is_default', 1)
            ->where('is_available', 1)
            ->first();

        if (!$serviceDelivery) {
            return null;
        }

        $deliveryMethod = $serviceDelivery->getDeliveryMethod();

        if (!$deliveryMethod) {
            return null;
        }

        return [
            'id' => $deliveryMethod->getID(),
            'name' => $deliveryMethod->name ?? '',
            'type' => $deliveryMethod->type ?? '',
            'additional_cost' => $serviceDelivery->getAdditionalCost(),
            'formatted_additional_cost' => $serviceDelivery->getFormattedAdditionalCost(),
            'total_cost' => $serviceDelivery->getTotalCost(),
            'formatted_total_cost' => $serviceDelivery->getFormattedTotalCost(),
            'estimated_days' => $serviceDelivery->getEstimatedDays(),
            'formatted_estimate' => $serviceDelivery->getFormattedEstimate(),
        ];
    }

    /**
     * Check if a service has required deliverables
     * 
     * @param int $serviceId
     * @return bool
     */
    public static function hasRequiredDeliverables($serviceId)
    {
        if (empty($serviceId)) {
            return false;
        }

        $serviceDeliverable = ServiceDeliverable::new()
            ->where('service_id', $serviceId)
            ->where('is_required', 1)
            ->first();

        return $serviceDeliverable !== null;
    }

    /**
     * Get digital deliverables for a service
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getDigitalDeliverables($serviceId)
    {
        $allDeliverables = self::getServiceDeliverables($serviceId);

        return array_filter($allDeliverables, function ($d) {
            return !empty($d['type']) && in_array(strtolower($d['type']), ['digital', 'download', 'file']);
        });
    }

    /**
     * Get physical deliverables for a service
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getPhysicalDeliverables($serviceId)
    {
        $allDeliverables = self::getServiceDeliverables($serviceId);

        return array_filter($allDeliverables, function ($d) {
            return !empty($d['type']) && in_array(strtolower($d['type']), ['physical', 'product', 'tangible']);
        });
    }

    /**
     * Calculate total estimated delivery time for a service
     * (based on default delivery method or longest deliverable time)
     * 
     * @param int $serviceId
     * @return int|null Days
     */
    public static function calculateServiceDeliveryTime($serviceId)
    {
        $defaultMethod = self::getDefaultDeliveryMethod($serviceId);

        if ($defaultMethod && isset($defaultMethod['estimated_days'])) {
            return (int)$defaultMethod['estimated_days'];
        }

        // Fall back to longest deliverable time
        $deliverables = self::getServiceDeliverables($serviceId);
        $maxDays = null;

        foreach ($deliverables as $d) {
            if (isset($d['delivery_days']) && ($maxDays === null || $d['delivery_days'] > $maxDays)) {
                $maxDays = (int)$d['delivery_days'];
            }
        }

        return $maxDays;
    }

    // ========================================================================
    // SUBSET - Deliverable, ServiceDelivery, DeliveryMethod, ServiceDelivery
    // ========================================================================

    /**
     * Get all related services for a given service (with relationship details)
     * 
     * @param int $serviceId
     * @param string|null $relationType Filter by specific relationship type
     * @return array
     */
    public static function getRelatedServices($serviceId, $relationType = null)
    {
        if (empty($serviceId)) {
            return [];
        }

        $query = ServiceRelationship::new()->where('parent_service_id', $serviceId);

        if (!empty($relationType)) {
            $query->where('relationship_type', $relationType);
        }

        $relationships = $query->orderBy('priority', 'ASC')->get();

        if ($relationships->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($relationships as $rel) {
            $relatedService = $rel->getRelatedService();

            if ($relatedService) {
                $result[] = [
                    'id' => $relatedService->getID(),
                    'name' => $relatedService->name ?? '',
                    'relationship_type' => $rel->getType(),
                    'formatted_type' => $rel->getFormattedType(),
                    'is_required' => $rel->isRequired(),
                    'is_bidirectional' => $rel->isBidirectional(),
                    'priority' => $rel->getPriority(),
                ];
            }
        }

        return $result;
    }

    /**
     * Get prerequisite services for a service
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getPrerequisiteServices($serviceId)
    {
        return self::getRelatedServices($serviceId, 'prerequisite');
    }

    /**
     * Get upsell services for a service
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getUpsellServices($serviceId)
    {
        return self::getRelatedServices($serviceId, 'upsell');
    }

    /**
     * Get cross-sell services for a service
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getCrossSellServices($serviceId)
    {
        return self::getRelatedServices($serviceId, 'cross-sell');
    }

    /**
     * Get all addons for a service with details
     * 
     * @param int $serviceId
     * @param bool $activeOnly
     * @return array
     */
    public static function getServiceAddons($serviceId, $activeOnly = true)
    {
        if (empty($serviceId)) {
            return [];
        }

        $addons = $activeOnly
            ? ServiceAddon::getActiveByService($serviceId)
            : ServiceAddon::getByService($serviceId);

        if ($addons->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($addons as $addon) {
            $result[] = [
                'id' => $addon->getID(),
                'name' => $addon->name ?? '',
                'slug' => $addon->slug ?? '',
                'description' => $addon->description ?? '',
                'price' => $addon->getPrice(),
                'formatted_price' => $addon->getFormattedPrice(),
                'formatted_price_with_interval' => $addon->getFormattedPriceWithInterval(),
                'is_free' => $addon->isFree(),
                'is_recurring' => $addon->isRecurring(),
                'recurring_interval' => $addon->getRecurringInterval(),
                'is_required' => $addon->isRequired(),
                'max_quantity' => $addon->getMaxQuantity(),
                'is_unlimited' => $addon->isUnlimitedQuantity(),
                'priority' => $addon->getPriority(),
            ];
        }

        // Sort by priority
        usort($result, function ($a, $b) {
            return $a['priority'] <=> $b['priority'];
        });

        return $result;
    }

    /**
     * Calculate total addon cost for a service configuration
     * 
     * @param int $serviceId
     * @param array $addonQuantities ['addon_id' => quantity]
     * @return float
     */
    public static function calculateAddonsCost($serviceId, $addonQuantities = [])
    {
        if (empty($serviceId) || empty($addonQuantities)) {
            return 0;
        }

        $total = 0;

        foreach ($addonQuantities as $addonId => $quantity) {
            $addon = ServiceAddon::new()->findById($addonId);

            if ($addon && $addon->service_id == $serviceId) {
                $addonPrice = $addon->calculatePrice($quantity);
                if ($addonPrice !== null) {
                    $total += $addonPrice;
                }
            }
        }

        return $total;
    }

    /**
     * Get all bundles containing a service
     * 
     * @param int $serviceId
     * @return array
     */
    public static function getBundlesContainingService($serviceId)
    {
        if (empty($serviceId)) {
            return [];
        }

        $bundleItems = BundleItem::getByService($serviceId);

        if ($bundleItems->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($bundleItems as $item) {
            $bundle = $item->getBundle();

            if ($bundle && $bundle->isCurrentlyAvailable()) {
                $result[] = [
                    'id' => $bundle->getID(),
                    'name' => $bundle->name ?? '',
                    'slug' => $bundle->slug ?? '',
                    'description' => $bundle->description ?? '',
                    'final_price' => $bundle->calculateFinalPrice(),
                    'formatted_price' => $bundle->getFormattedFinalPrice(),
                    'discount_percentage' => $bundle->getDiscountPercentage(),
                    'formatted_discount' => $bundle->getFormattedDiscount(),
                    'savings' => $bundle->calculateSavings(),
                    'formatted_savings' => $bundle->getFormattedSavings(),
                    'service_count' => $bundle->getServiceCount(),
                    'item_quantity' => $item->getQuantity(),
                ];
            }
        }

        return $result;
    }

    /**
     * Get bundle details with all included services
     * 
     * @param int $bundleId
     * @return array|null
     */
    public static function getBundleDetails($bundleId)
    {
        if (empty($bundleId)) {
            return null;
        }

        $bundle = ServiceBundle::new()->findById($bundleId);

        if (!$bundle) {
            return null;
        }

        $services = $bundle->getServices();
        $itemsTotal = $bundle->calculateItemsTotal();
        $finalPrice = $bundle->calculateFinalPrice();

        return [
            'id' => $bundle->getID(),
            'name' => $bundle->name ?? '',
            'slug' => $bundle->slug ?? '',
            'description' => $bundle->description ?? '',
            'status' => $bundle->status ?? '',
            'is_available' => $bundle->isCurrentlyAvailable(),
            'services' => $services,
            'service_count' => count($services),
            'items_total' => $itemsTotal,
            'formatted_items_total' => '$' . number_format($itemsTotal, 2),
            'discount_percentage' => $bundle->getDiscountPercentage(),
            'formatted_discount' => $bundle->getFormattedDiscount(),
            'final_price' => $finalPrice,
            'formatted_price' => $bundle->getFormattedFinalPrice(),
            'savings' => $bundle->calculateSavings(),
            'formatted_savings' => $bundle->getFormattedSavings(),
            'start_date' => $bundle->start_date ?? null,
            'end_date' => $bundle->end_date ?? null,
        ];
    }

    /**
     * Check if a service has any prerequisites
     * 
     * @param int $serviceId
     * @return bool
     */
    public static function hasPrerequisites($serviceId)
    {
        if (empty($serviceId)) {
            return false;
        }

        $prerequisite = ServiceRelationship::new()
            ->where('parent_service_id', $serviceId)
            ->where('relationship_type', 'prerequisite')
            ->where('is_required', 1)
            ->first();

        return $prerequisite !== null;
    }

    /**
     * Get all available bundles (currently active)
     * 
     * @return array
     */
    public static function getAvailableBundles()
    {
        $bundles = ServiceBundle::getCurrentlyAvailable();

        if ($bundles->isEmpty()) {
            return [];
        }

        $result = [];

        foreach ($bundles as $bundle) {
            $result[] = [
                'id' => $bundle->getID(),
                'name' => $bundle->name ?? '',
                'slug' => $bundle->slug ?? '',
                'description' => $bundle->description ?? '',
                'final_price' => $bundle->calculateFinalPrice(),
                'formatted_price' => $bundle->getFormattedFinalPrice(),
                'discount_percentage' => $bundle->getDiscountPercentage(),
                'formatted_discount' => $bundle->getFormattedDiscount(),
                'savings' => $bundle->calculateSavings(),
                'formatted_savings' => $bundle->getFormattedSavings(),
                'service_count' => $bundle->getServiceCount(),
            ];
        }

        return $result;
    }

    /**
     * Calculate total service cost with addons and delivery
     * 
     * @param int $serviceId
     * @param int|null $pricingTierId
     * @param int|null $pricingModelId
     * @param array $addonQuantities ['addon_id' => quantity]
     * @param int|null $deliveryMethodId
     * @return float|null
     */
    public static function calculateTotalServiceCost($serviceId, $pricingTierId = null, $pricingModelId = null, $addonQuantities = [], $deliveryMethodId = null)
    {
        if (empty($serviceId)) {
            return null;
        }

        $total = 0;

        // Base service price (requires both tier and model)
        if (!empty($pricingTierId) && !empty($pricingModelId)) {
            $servicePrice = ServicePrice::new()->getCurrentPriceFor($serviceId, $pricingTierId, $pricingModelId);
            if ($servicePrice) {
                $total += $servicePrice->amount ?? 0;
                $total += $servicePrice->setup_fee ?? 0;
            }
        }

        // Addons
        $addonsCost = self::calculateAddonsCost($serviceId, $addonQuantities);
        $total += $addonsCost;

        // Delivery method additional cost
        if (!empty($deliveryMethodId)) {
            $serviceDelivery = ServiceDelivery::findRelationship($serviceId, $deliveryMethodId);
            if ($serviceDelivery) {
                $deliveryCost = $serviceDelivery->getTotalCost();
                if ($deliveryCost !== null) {
                    $total += $deliveryCost;
                }
            }
        }

        return $total;
    }

    /**
     * Get formatted total service cost breakdown
     * 
     * @param int $serviceId
     * @param int|null $pricingTierId
     * @param int|null $pricingModelId
     * @param array $addonQuantities ['addon_id' => quantity]
     * @param int|null $deliveryMethodId
     * @param string $currencySymbol
     * @return array
     */
    public static function getServiceCostBreakdown($serviceId, $pricingTierId = null, $pricingModelId = null, $addonQuantities = [], $deliveryMethodId = null, $currencySymbol = '$')
    {
        if (empty($serviceId)) {
            return [];
        }

        $breakdown = [
            'base_price' => 0,
            'setup_fee' => 0,
            'addons_cost' => 0,
            'delivery_cost' => 0,
            'subtotal' => 0,
            'total' => 0,
            'currency' => null,
            'unit' => null,
            'formatted' => [],
        ];

        // Base service price (requires both tier and model)
        if (!empty($pricingTierId) && !empty($pricingModelId)) {
            $servicePrice = ServicePrice::new()->getCurrentPriceFor($serviceId, $pricingTierId, $pricingModelId);
            if ($servicePrice) {
                $breakdown['base_price'] = $servicePrice->amount ?? 0;
                $breakdown['setup_fee'] = $servicePrice->setup_fee ?? 0;
                $breakdown['currency'] = $servicePrice->currency ?? null;
                $breakdown['unit'] = $servicePrice->unit ?? null;
            }
        }

        // Addons
        $breakdown['addons_cost'] = self::calculateAddonsCost($serviceId, $addonQuantities);

        // Delivery
        if (!empty($deliveryMethodId)) {
            $serviceDelivery = ServiceDelivery::findRelationship($serviceId, $deliveryMethodId);
            if ($serviceDelivery) {
                $breakdown['delivery_cost'] = $serviceDelivery->getTotalCost() ?? 0;
            }
        }

        // Calculate totals
        $breakdown['subtotal'] = $breakdown['base_price'] + $breakdown['addons_cost'] + $breakdown['delivery_cost'];
        $breakdown['total'] = $breakdown['subtotal'] + $breakdown['setup_fee'];

        // Formatted versions
        $breakdown['formatted'] = [
            'base_price' => $currencySymbol . number_format($breakdown['base_price'], 2),
            'setup_fee' => $currencySymbol . number_format($breakdown['setup_fee'], 2),
            'addons_cost' => $currencySymbol . number_format($breakdown['addons_cost'], 2),
            'delivery_cost' => $currencySymbol . number_format($breakdown['delivery_cost'], 2),
            'subtotal' => $currencySymbol . number_format($breakdown['subtotal'], 2),
            'total' => $currencySymbol . number_format($breakdown['total'], 2),
        ];

        return $breakdown;
    }
}
