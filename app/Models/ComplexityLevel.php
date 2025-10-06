<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;
use TypeRocket\Models\WPUser;

class ComplexityLevel extends Model
{
    protected $resource = 'srvc_complexity_levels';

    protected $fillable = [
        'name',
        'level',
        'price_multiplier'
    ];

    protected $guard = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $format = [
        'level' => 'convertEmptyToNull',
        'price_multiplier' => 'convertEmptyToNull'
    ];

    protected $with = [
        'services'
    ];

    // Get all services using this complexity level
    public function services()
    {
        return $this->hasMany(Service::class, 'complexity_id');
    }

    // User who last updated this record
    public function updatedBy()
    {
        return $this->belongsTo(WPUser::class, 'updated_by');
    }

    // User who created this record
    public function createdBy()
    {
        return $this->belongsTo(WPUser::class, 'created_by');
    }

    /**
     * Get all active complexity levels ordered by level
     */
    public function getActive()
    {
        return $this->where('deleted_at', 'IS', null)
            ->orderBy('level', 'ASC')
            ->findAll();
    }

    /**
     * Find complexity level by level number
     */
    public function findByLevel($levelNumber)
    {
        return $this->where('level', $levelNumber)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get complexity level by name
     */
    public function findByName($name)
    {
        return $this->where('name', $name)
            ->where('deleted_at', 'IS', null)
            ->first();
    }

    /**
     * Get levels within a range
     */
    public function getLevelsInRange($minLevel, $maxLevel)
    {
        return $this->where('level', '>=', $minLevel)
            ->where('level', '<=', $maxLevel)
            ->where('deleted_at', 'IS', null)
            ->orderBy('level', 'ASC')
            ->findAll();
    }

    /**
     * Get levels with multiplier above threshold
     */
    public function getLevelsAboveMultiplier($threshold)
    {
        return $this->where('price_multiplier', '>=', $threshold)
            ->where('deleted_at', 'IS', null)
            ->orderBy('level', 'ASC')
            ->findAll();
    }

    /**
     * Calculate adjusted price using this complexity multiplier
     */
    public function calculateAdjustedPrice($basePrice)
    {
        return $basePrice * $this->price_multiplier;
    }

    /**
     * Get price impact as percentage
     */
    public function getPriceImpactPercent()
    {
        return round(($this->price_multiplier - 1) * 100, 1);
    }

    /**
     * Get formatted multiplier display
     */
    public function getMultiplierDisplay()
    {
        $impact = $this->getPriceImpactPercent();

        if ($impact == 0) {
            return 'No price adjustment';
        } elseif ($impact > 0) {
            return "+{$impact}% price increase";
        } else {
            return "{$impact}% price reduction";
        }
    }

    /**
     * Check if this complexity level increases price
     */
    public function increasesPrice()
    {
        return $this->price_multiplier > 1.0;
    }

    /**
     * Check if this complexity level decreases price
     */
    public function decreasesPrice()
    {
        return $this->price_multiplier < 1.0;
    }

    /**
     * Check if this is a neutral pricing level
     */
    public function isNeutral()
    {
        return $this->price_multiplier == 1.0;
    }

    /**
     * Get next higher complexity level
     */
    public function getNextLevel()
    {
        return static::new()
            ->where('level', '>', $this->level)
            ->where('deleted_at', 'IS', null)
            ->orderBy('level', 'ASC')
            ->first();
    }

    /**
     * Get previous lower complexity level
     */
    public function getPreviousLevel()
    {
        return static::new()
            ->where('level', '<', $this->level)
            ->where('deleted_at', 'IS', null)
            ->orderBy('level', 'DESC')
            ->first();
    }

    /**
     * Check if this is the lowest complexity level
     */
    public function isLowest()
    {
        $lowest = static::new()
            ->where('deleted_at', 'IS', null)
            ->orderBy('level', 'ASC')
            ->first();

        return $lowest && $lowest->id == $this->id;
    }

    /**
     * Check if this is the highest complexity level
     */
    public function isHighest()
    {
        $highest = static::new()
            ->where('deleted_at', 'IS', null)
            ->orderBy('level', 'DESC')
            ->first();

        return $highest && $highest->id == $this->id;
    }

    /**
     * Get service count for this complexity level
     */
    public function getServiceCount($activeOnly = true)
    {
        $services = $this->services;

        if (!$services) {
            return 0;
        }

        if ($activeOnly) {
            $count = 0;
            foreach ($services as $service) {
                if ($service->is_active && $service->deleted_at === null) {
                    $count++;
                }
            }
            return $count;
        }

        return count($services);
    }

    /**
     * Get active services for this complexity level
     */
    public function getActiveServices()
    {
        $services = $this->services;

        if (!$services) {
            return [];
        }

        $filtered = [];
        foreach ($services as $service) {
            if ($service->is_active && $service->deleted_at === null) {
                $filtered[] = $service;
            }
        }

        return $filtered;
    }

    /**
     * Get average service price for this complexity level
     */
    public function getAverageServicePrice($currency = 'CAD')
    {
        $services = $this->getActiveServices();

        if (empty($services)) {
            return null;
        }

        $totalPrice = 0;
        $count = 0;

        foreach ($services as $service) {
            $prices = $service->prices;
            if ($prices) {
                foreach ($prices as $price) {
                    if (
                        $price->currency === $currency &&
                        $price->is_current == 1 &&
                        $price->approval_status === 'approved'
                    ) {

                        // Calculate with complexity multiplier
                        $adjustedPrice = $price->amount * $this->price_multiplier;
                        $totalPrice += $adjustedPrice;
                        $count++;
                        break;
                    }
                }
            }
        }

        return $count > 0 ? round($totalPrice / $count, 2) : null;
    }

    /**
     * Compare this level to another
     * Returns: negative if lower, 0 if equal, positive if higher
     */
    public function compareTo(ComplexityLevel $other)
    {
        return $this->level - $other->level;
    }

    /**
     * Get complexity badge for display
     */
    public function getComplexityBadge()
    {
        $impact = $this->getPriceImpactPercent();
        $class = 'complexity-badge';

        if ($impact < 0) {
            $class .= ' discount';
        } elseif ($impact == 0) {
            $class .= ' neutral';
        } elseif ($impact < 50) {
            $class .= ' moderate';
        } elseif ($impact < 100) {
            $class .= ' high';
        } else {
            $class .= ' premium';
        }

        return sprintf(
            '<span class="%s">Level %d: %s (%s)</span>',
            $class,
            $this->level,
            $this->name,
            $this->getMultiplierDisplay()
        );
    }

    /**
     * Update multiplier
     */
    public function updateMultiplier($newMultiplier)
    {
        if ($newMultiplier < 0 || $newMultiplier > 99.9) {
            throw new \InvalidArgumentException("Multiplier must be between 0 and 99.9");
        }

        $this->price_multiplier = $newMultiplier;
        return $this->update(['price_multiplier']);
    }

    /**
     * Update level number
     */
    public function updateLevel($newLevel)
    {
        if ($newLevel < 0 || $newLevel > 255) {
            throw new \InvalidArgumentException("Level must be between 0 and 255");
        }

        // Check if level number is unique
        $existing = static::new()
            ->where('level', $newLevel)
            ->where('id', '!=', $this->id)
            ->where('deleted_at', 'IS', null)
            ->first();

        if ($existing) {
            throw new \RuntimeException("Level {$newLevel} is already in use by '{$existing->name}'");
        }

        $this->level = $newLevel;
        return $this->update(['level']);
    }

    /**
     * Validate complexity level data
     */
    public function validateComplexity()
    {
        $errors = [];

        if ($this->level < 0 || $this->level > 255) {
            $errors[] = "Level must be between 0 and 255";
        }

        if ($this->price_multiplier < 0 || $this->price_multiplier > 99.9) {
            $errors[] = "Price multiplier must be between 0 and 99.9";
        }

        // Check unique level
        $existing = static::new()
            ->where('level', $this->level)
            ->where('id', '!=', $this->id ?? 0)
            ->where('deleted_at', 'IS', null)
            ->first();

        if ($existing) {
            $errors[] = "Level {$this->level} is already in use";
        }

        // Check unique name
        $existing = static::new()
            ->where('name', $this->name)
            ->where('id', '!=', $this->id ?? 0)
            ->where('deleted_at', 'IS', null)
            ->first();

        if ($existing) {
            $errors[] = "Name '{$this->name}' is already in use";
        }

        return empty($errors) ? true : $errors;
    }

    /**
     * Check if complexity level is in use
     */
    public function isInUse()
    {
        return $this->getServiceCount(false) > 0;
    }

    /**
     * Soft delete (with validation)
     */
    public function softDelete()
    {
        if ($this->isInUse()) {
            throw new \RuntimeException(
                "Cannot delete complexity level '{$this->name}' because it is in use by {$this->getServiceCount(false)} service(s)"
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
     * Get complexity statistics
     */
    public static function getComplexityStats()
    {
        $levels = static::new()->getActive()->get();

        if (!$levels) {
            return null;
        }

        $stats = [
            'total_levels' => count($levels),
            'min_multiplier' => null,
            'max_multiplier' => null,
            'avg_multiplier' => 0,
            'levels_with_services' => 0,
            'total_services' => 0
        ];

        $multipliers = [];

        foreach ($levels as $level) {
            $multipliers[] = $level->price_multiplier;
            $serviceCount = $level->getServiceCount(true);

            if ($serviceCount > 0) {
                $stats['levels_with_services']++;
                $stats['total_services'] += $serviceCount;
            }
        }

        $stats['min_multiplier'] = min($multipliers);
        $stats['max_multiplier'] = max($multipliers);
        $stats['avg_multiplier'] = round(array_sum($multipliers) / count($multipliers), 2);

        return $stats;
    }

    /**
     * Get all complexity levels as select options
     */
    public static function getSelectOptions($includeEmpty = true)
    {
        $options = [];

        if ($includeEmpty) {
            $options[''] = '— Select Complexity Level —';
        }

        $levels = static::new()->getActive()->get();

        if ($levels) {
            foreach ($levels as $level) {
                $options[$level->id] = sprintf(
                    'Level %d: %s (×%s)',
                    $level->level,
                    $level->name,
                    number_format($level->price_multiplier, 2)
                );
            }
        }

        return $options;
    }
}
