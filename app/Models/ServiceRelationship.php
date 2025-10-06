<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class ServiceRelationship extends Model
{
    protected $resource = 'srvc_service_relationships';


    protected $fillable = [
        'service_id',
        'related_service_id',
        'relation_type',
        'notes'
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
        'relatedService'
    ];

    /** ServiceRelationship belongs to a Service (the primary service) */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceRelationship belongs to a Service (the related service) */
    public function relatedService()
    {
        return $this->belongsTo(Service::class, 'related_service_id');
    }

    /** Created by WP user */
    public function createdBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'created_by');
    }

    /** Updated by WP user */
    public function updatedBy()
    {
        return $this->belongsTo(\TypeRocket\Models\WPUser::class, 'updated_by');
    }

    /**
     * Get inverse relationship type mapping
     */
    protected static function getInverseRelationTypes()
    {
        return [
            'prerequisite' => 'enables',
            'dependency' => 'enables',
            'incompatible_with' => 'incompatible_with',
            'substitute_for' => 'substitute_for',
            'complements' => 'complements',
            'replaces' => 'replaced_by',
            'requires' => 'enables',
            'enables' => 'dependency',
            'conflicts_with' => 'conflicts_with',
            'replaced_by' => 'replaces' // Add this to your enum if needed
        ];
    }

    /**
     * Find all relationships for a service (both directions)
     * 
     * @param int $serviceId
     * @param string|null $relationType Filter by relationship type
     * @return \TypeRocket\Database\Results
     */
    public static function findBidirectional($serviceId, $relationType = null)
    {
        $query = static::new();

        // Find relationships where service is either the primary or related service
        $query->where(function ($q) use ($serviceId) {
            $q->where('service_id', $serviceId)
                ->orWhere('related_service_id', $serviceId);
        });

        if ($relationType) {
            $inverseType = static::getInverseRelationTypes()[$relationType] ?? null;

            $query->where(function ($q) use ($serviceId, $relationType, $inverseType) {
                // Direct relationship
                $q->where('service_id', $serviceId)->where('relation_type', $relationType);

                // Or inverse relationship
                if ($inverseType && $inverseType !== $relationType) {
                    $q->orWhere('related_service_id', $serviceId)->where('relation_type', $inverseType);
                }
                // For symmetric relationships (incompatible_with, etc.)
                elseif ($inverseType === $relationType) {
                    $q->orWhere('related_service_id', $serviceId)->where('relation_type', $relationType);
                }
            });
        }

        return $query->with(['service', 'relatedService'])->get();
    }

    /**
     * Get related services for a specific service with relationship context
     * 
     * @param int $serviceId
     * @param string|null $relationType
     * @return array
     */
    public static function getRelatedServicesWithContext($serviceId, $relationType = null)
    {
        $relationships = static::findBidirectional($serviceId, $relationType);
        $results = [];

        foreach ($relationships as $relationship) {
            $isReverse = ($relationship->related_service_id == $serviceId);
            $relatedService = $isReverse ? $relationship->service : $relationship->relatedService;
            $contextType = $relationship->relation_type;

            // If this is a reverse relationship, get the contextual type
            if ($isReverse) {
                $inverseTypes = static::getInverseRelationTypes();
                $contextType = $inverseTypes[$relationship->relation_type] ?? $relationship->relation_type;
            }

            $results[] = [
                'service' => $relatedService,
                'relationship_type' => $contextType,
                'notes' => $relationship->notes,
                'is_reverse' => $isReverse,
                'relationship_id' => $relationship->id
            ];
        }

        return $results;
    }

    /**
     * Check if two services have a specific relationship (bidirectional)
     * 
     * @param int $serviceId1
     * @param int $serviceId2  
     * @param string $relationType
     * @return bool
     */
    public static function hasRelationship($serviceId1, $serviceId2, $relationType)
    {
        $inverseType = static::getInverseRelationTypes()[$relationType] ?? null;

        $query = static::new()->where(function ($q) use ($serviceId1, $serviceId2, $relationType, $inverseType) {
            // Direct relationship
            $q->where('service_id', $serviceId1)
                ->where('related_service_id', $serviceId2)
                ->where('relation_type', $relationType);

            // Inverse relationship
            if ($inverseType) {
                $q->orWhere('service_id', $serviceId2)
                    ->where('related_service_id', $serviceId1)
                    ->where('relation_type', $inverseType);
            }
        });

        return $query->first() !== null;
    }

    /**
     * Get all relationships for a service (as parent)
     * 
     * @param int $serviceId
     */
    public static function getByParentService($serviceId)
    {
        if (empty($serviceId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('parent_service_id', $serviceId)->get();
    }

    /**
     * Get all relationships for a service (as related/child)
     * 
     * @param int $serviceId
     */
    public static function getByRelatedService($serviceId)
    {
        if (empty($serviceId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()->where('related_service_id', $serviceId)->get();
    }

    /**
     * Get all relationships for a service (both directions)
     * 
     * @param int $serviceId
     */
    public static function getByService($serviceId)
    {
        if (empty($serviceId)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()
            ->where('parent_service_id', $serviceId)
            ->orWhere('related_service_id', $serviceId)
            ->get();
    }

    /**
     * Find relationships by type
     * 
     * @param int $serviceId
     * @param string $relationType
     */
    public static function getByType($serviceId, $relationType)
    {
        if (empty($serviceId) || empty($relationType)) {
            return static::new()->where('1', '0')->get();
        }

        return static::new()
            ->where('parent_service_id', $serviceId)
            ->where('relationship_type', $relationType)
            ->get();
    }

    /**
     * Find specific relationship between two services
     * 
     * @param int $parentServiceId
     * @param int $relatedServiceId
     * @return static|null
     */
    public static function findRelationship($parentServiceId, $relatedServiceId)
    {
        if (empty($parentServiceId) || empty($relatedServiceId)) {
            return null;
        }

        return static::new()
            ->where('parent_service_id', $parentServiceId)
            ->where('related_service_id', $relatedServiceId)
            ->first();
    }

    /**
     * Get the parent Service model
     * 
     * @return Service|null
     */
    public function getParentService()
    {
        if (empty($this->parent_service_id)) {
            return null;
        }

        return Service::new()->findById($this->parent_service_id);
    }

    /**
     * Get the related Service model
     * 
     * @return Service|null
     */
    public function getRelatedService()
    {
        if (empty($this->related_service_id)) {
            return null;
        }

        return Service::new()->findById($this->related_service_id);
    }

    /**
     * Get the relationship type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->relationship_type ?? '';
    }

    /**
     * Check if relationship is prerequisite type
     * 
     * @return bool
     */
    public function isPrerequisite()
    {
        return strtolower($this->getType()) === 'prerequisite';
    }

    /**
     * Check if relationship is upsell type
     * 
     * @return bool
     */
    public function isUpsell()
    {
        return strtolower($this->getType()) === 'upsell';
    }

    /**
     * Check if relationship is cross-sell type
     * 
     * @return bool
     */
    public function isCrossSell()
    {
        return strtolower($this->getType()) === 'cross-sell';
    }

    /**
     * Check if relationship is alternative type
     * 
     * @return bool
     */
    public function isAlternative()
    {
        return strtolower($this->getType()) === 'alternative';
    }

    /**
     * Check if relationship is complement type
     * 
     * @return bool
     */
    public function isComplement()
    {
        return strtolower($this->getType()) === 'complement';
    }

    /**
     * Check if relationship is required
     * 
     * @return bool
     */
    public function isRequired()
    {
        return !empty($this->is_required) && (bool)$this->is_required;
    }

    /**
     * Check if relationship is optional
     * 
     * @return bool
     */
    public function isOptional()
    {
        return !$this->isRequired();
    }

    /**
     * Get relationship priority/sort order
     * 
     * @return int
     */
    public function getPriority()
    {
        return isset($this->priority) ? (int)$this->priority : 999;
    }

    /**
     * Check if relationship is bidirectional
     * 
     * @return bool
     */
    public function isBidirectional()
    {
        return !empty($this->is_bidirectional) && (bool)$this->is_bidirectional;
    }

    /**
     * Get the reverse relationship (if bidirectional)
     * 
     * @return static|null
     */
    public function getReverseRelationship()
    {
        if (!$this->isBidirectional()) {
            return null;
        }

        return static::findRelationship($this->related_service_id, $this->parent_service_id);
    }

    /**
     * Get formatted relationship description
     * 
     * @return string
     */
    public function getFormattedType()
    {
        $type = $this->getType();

        $labels = [
            'prerequisite' => 'Prerequisite',
            'upsell' => 'Upsell',
            'cross-sell' => 'Cross-sell',
            'alternative' => 'Alternative',
            'complement' => 'Complement',
            'related' => 'Related',
        ];

        return $labels[strtolower($type)] ?? ucfirst($type);
    }

    /**
     * Validate service relationship
     * 
     * @return array Array of error messages (empty if valid)
     */
    public function validate()
    {
        $errors = [];

        if (empty($this->parent_service_id)) {
            $errors[] = 'Parent service ID is required';
        }

        if (empty($this->related_service_id)) {
            $errors[] = 'Related service ID is required';
        }

        if (!empty($this->parent_service_id) && !empty($this->related_service_id)) {
            if ($this->parent_service_id === $this->related_service_id) {
                $errors[] = 'A service cannot be related to itself';
            }

            $existing = static::findRelationship($this->parent_service_id, $this->related_service_id);
            if ($existing && $existing->getID() !== $this->getID()) {
                $errors[] = 'This service relationship already exists';
            }
        }

        if (empty($this->relationship_type)) {
            $errors[] = 'Relationship type is required';
        }

        return $errors;
    }
}
