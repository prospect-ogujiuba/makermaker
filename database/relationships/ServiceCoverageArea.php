<?php

// ARTIFACT: app/Models/ServiceCoverageArea.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceCoverageArea has many ServiceCoverages */
    public function serviceCoverages()
    {
        return $this->hasMany(ServiceCoverage::class, 'coverage_area_id');
    }

    /** Created by WP user */
    public function createdBy()