<?php

namespace MakerMaker\Models;

use TypeRocket\Models\Model;

class Client extends Model
{
    protected $resource = 'b2bcnc_clients';

    protected $fillable = [
        'company_name',
        'legal_name',
        'business_number',
        'contact_firstname',
        'contact_lastname',
        'contact_title',
        'email',
        'phone',
        'mobile',
        'website',
        'street',
        'city',
        'province',
        'postal_code',
        'country',
        'industry',
        'company_size',
        'annual_revenue',
        'billing_street',
        'billing_city',
        'billing_province',
        'billing_postal_code',
        'billing_country',
        'tax_number',
        'payment_terms',
        'credit_limit',
        'notes',
        'status',
        'priority',
        'source',
        'assigned_to',
        'onboarded_at',
        'last_contact_at'
    ];

    protected $guard = [
        'id',
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    // Cast data types for certain fields
    protected $cast = [
        'credit_limit' => 'float',
        'assigned_to' => 'int',
        'onboarded_at' => 'datetime',
        'last_contact_at' => 'datetime'
    ];

    // Format fields for database storage
    protected $format = [
        'onboarded_at' => 'datetime',
        'last_contact_at' => 'datetime'
    ];

    // Relationships
    public function assignedUser()
    {
        // This would link to WordPress users table if needed
        // return $this->belongsTo('\App\Models\User', 'assigned_to', 'ID');
    }

    // Scopes for common queries
    public function scopeActive($query)
    {
        return $query->where('status', '=', 'active');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', '=', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', '=', $priority);
    }

    // Helper methods
    public function getFullContactName()
    {
        return $this->contact_firstname . ' ' . $this->contact_lastname;
    }

    public function getFormattedPhone()
    {
        if ($this->phone && strlen($this->phone) === 10) {
            return '(' . substr($this->phone, 0, 3) . ') ' . 
                   substr($this->phone, 3, 3) . '-' . 
                   substr($this->phone, 6, 4);
        }
        return $this->phone;
    }

    public function getBillingAddress()
    {
        if ($this->billing_street) {
            return $this->billing_street . ', ' . 
                   $this->billing_city . ', ' . 
                   $this->billing_province . ' ' . 
                   $this->billing_postal_code;
        }
        
        // Fall back to physical address
        return $this->street . ', ' . 
               $this->city . ', ' . 
               $this->province . ' ' . 
               $this->postal_code;
    }
}