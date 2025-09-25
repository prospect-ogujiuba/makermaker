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
        $query->where(function($q) use ($serviceId) {
            $q->where('service_id', $serviceId)
              ->orWhere('related_service_id', $serviceId);
        });

        if ($relationType) {
            $inverseType = static::getInverseRelationTypes()[$relationType] ?? null;
            
            $query->where(function($q) use ($serviceId, $relationType, $inverseType) {
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

        $query = static::new()->where(function($q) use ($serviceId1, $serviceId2, $relationType, $inverseType) {
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
}
