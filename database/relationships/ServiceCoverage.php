<?php

// ARTIFACT: app/Models/ServiceCoverage.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceCoverage belongs to a Service */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /** ServiceCoverage belongs to a ServiceCoverageArea */
    public function coverageArea()
    {
        return $this->belongsTo(ServiceCoverageArea::class, 'coverage_area_id');
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

    // === Relationships:END ===