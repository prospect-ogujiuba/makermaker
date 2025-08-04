<?php

namespace MakerMaker\Models\Traits;

/**
 * HasActiveStatus Trait
 * 
 * Provides active/inactive status functionality for models.
 * Includes scopes and helper methods for managing status.
 */
trait HasActiveStatus
{
    /**
     * Activate the model
     * 
     * @return $this
     */
    public function activate()
    {
        $this->active = true;
        $this->save(['active' => true]);
        
        return $this;
    }

    /**
     * Deactivate the model
     * 
     * @return $this
     */
    public function deactivate()
    {
        $this->active = false;
        $this->save(['active' => false]);
        
        return $this;
    }

    /**
     * Toggle the active status
     * 
     * @return $this
     */
    public function toggleActive()
    {
        $this->active = !$this->active;
        $this->save(['active' => $this->active]);
        
        return $this;
    }

    /**
     * Check if model is active
     * 
     * @return bool True if active, false otherwise
     */
    public function isActive(): bool
    {
        return (bool) $this->active;
    }

    /**
     * Check if model is inactive
     * 
     * @return bool True if inactive, false otherwise
     */
    public function isInactive(): bool
    {
        return !$this->isActive();
    }

    /**
     * Scope to get only active records
     * Usage: Model::new()->active()->get()
     * 
     * @return $this
     */
    public function active()
    {
        return $this->where('active', '=', 1);
    }

    /**
     * Scope to get only inactive records
     * Usage: Model::new()->inactive()->get()
     * 
     * @return $this
     */
    public function inactive()
    {
        return $this->where('active', '=', 0);
    }

    /**
     * Scope to get active records (alias for active())
     * Usage: Model::new()->whereActive()->get()
     * 
     * @return $this
     */
    public function whereActive()
    {
        return $this->active();
    }

    /**
     * Scope to get inactive records (alias for inactive())
     * Usage: Model::new()->whereInactive()->get()
     * 
     * @return $this
     */
    public function whereInactive()
    {
        return $this->inactive();
    }
}