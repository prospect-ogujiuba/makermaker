<?php

// ARTIFACT: app/Models/ServiceCategory.php (relationships)
    
    // === Relationships:BEGIN ===

    /** ServiceCategory has many Services */
    public function services()
    {
        return $this->hasMany(Service::class, 'category_id');
    }

    /** ServiceCategory belongs to parent ServiceCategory */
    public function parentCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }

    /** ServiceCategory has many child ServiceCategories */
    public function childCategories()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
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