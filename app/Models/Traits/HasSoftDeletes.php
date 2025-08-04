<?php

namespace MakerMaker\Models\Traits;

/**
 * HasSoftDeletes Trait
 * 
 * Provides soft delete functionality for models.
 * Soft deleted records are marked with a timestamp but not physically removed.
 */
trait HasSoftDeletes
{
    /**
     * Soft delete the model
     * Sets deleted_at timestamp instead of removing from database
     * 
     * @return $this
     */
    public function softDelete()
    {
        if ($this->isDeleted()) {
            return $this; // Already deleted
        }

        $this->deleted_at = $this->getDateTime();
        $this->save(['deleted_at' => $this->deleted_at]);
        
        // Deactivate if model has active field
        if (property_exists($this, 'active') || in_array('active', $this->fillable ?? [])) {
            $this->save(['active' => '0']);
        }

        return $this;
    }

    /**
     * Restore a soft deleted model
     * Sets deleted_at to null
     * 
     * @return $this
     */
    public function restore()
    {
        if (!$this->isDeleted()) {
            return $this; // Not deleted
        }

        $this->deleted_at = null;
        $this->save(['deleted_at' => null]);

        return $this;
    }

    /**
     * Permanently delete the model from database
     * This cannot be undone
     * 
     * @return bool
     */
    public function forceDelete()
    {
        return parent::delete();
    }

    /**
     * Check if model is soft deleted
     * 
     * @return bool True if soft deleted, false otherwise
     */
    public function isDeleted(): bool
    {
        return !is_null($this->deleted_at);
    }

    /**
     * Scope to exclude soft deleted records
     * Usage: Model::new()->withoutDeleted()->get()
     * 
     * @return $this
     */
    public function withoutDeleted()
    {
        return $this->where('deleted_at', 'IS', null);
    }

    /**
     * Scope to include only soft deleted records
     * Usage: Model::new()->onlyDeleted()->get()
     * 
     * @return $this
     */
    public function onlyDeleted()
    {
        return $this->where('deleted_at', 'IS NOT', null);
    }

    /**
     * Scope to include soft deleted records
     * Usage: Model::new()->withDeleted()->get()
     * 
     * @return $this
     */
    public function withDeleted()
    {
        // Return self without any deleted_at filter
        return $this;
    }

    /**
     * Override the default delete method to use soft delete
     * 
     * @param mixed $ids Optional IDs parameter to match parent signature
     * @return $this
     */
    public function delete($ids = null)
    {
        // If $ids is provided, delegate to parent for bulk operations
        if ($ids !== null) {
            return parent::delete($ids);
        }
        
        // Otherwise use soft delete for single instance
        return $this->softDelete();
    }

    /**
     * Get a fresh timestamp for the model
     * 
     * @return string Current timestamp
     */
    public function getDateTime(): string
    {
        return date('Y-m-d H:i:s');
    }
}