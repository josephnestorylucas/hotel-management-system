<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Schema;

/**
 * Custom soft-delete trait that keeps both a boolean `is_deleted` column
 * and Laravel's default `deleted_at` timestamp in sync.
 *
 * Usage:
 *   use HasSoftDelete;
 *
 * Every query automatically excludes soft-deleted records via a global scope
 * on `is_deleted = false` (in addition to the standard whereNull('deleted_at')).
 *
 * All scopes gracefully skip columns that don't exist yet (e.g. during tests
 * before migrations have run).
 */
trait HasSoftDelete
{
    use SoftDeletes;

    /**
     * Override the SoftDeletes boot to make it column-aware.
     * Only registers the SoftDeletingScope if the `deleted_at` column exists.
     */
    public static function bootSoftDeletes(): void
    {
        // Skip the default SoftDeletingScope registration — we handle it ourselves
        // in bootHasSoftDelete() so both columns are guarded together.
    }

    /**
     * Boot the trait.
     * Register a global scope that filters out soft-deleted records
     * by both `is_deleted` and `deleted_at` columns, but only when they exist.
     */
    protected static function bootHasSoftDelete(): void
    {
        static::addGlobalScope('soft_delete_filter', function (Builder $builder) {
            $table = $builder->getModel()->getTable();

            if (Schema::hasColumn($table, 'is_deleted')) {
                $builder->where("{$table}.is_deleted", false);
            }

            if (Schema::hasColumn($table, 'deleted_at')) {
                $builder->whereNull("{$table}.deleted_at");
            }
        });
    }

    /**
     * Perform the actual soft-delete on the model.
     * Sets both `deleted_at` and `is_deleted` so they are always in sync.
     */
    protected function runSoftDelete(): void
    {
        $time = $this->freshTimestamp();

        if (Schema::hasColumn($this->getTable(), 'deleted_at')) {
            $this->{$this->getDeletedAtColumn()} = $this->fromDateTime($time);
        }

        if (Schema::hasColumn($this->getTable(), 'is_deleted')) {
            $this->is_deleted = true;
        }

        $this->save();

        $this->exists = true;
    }

    /**
     * Restore a soft-deleted model.
     * Clears both `deleted_at` and `is_deleted`.
     */
    public function restore(): bool
    {
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        if (Schema::hasColumn($this->getTable(), 'deleted_at')) {
            $this->{$this->getDeletedAtColumn()} = null;
        }

        if (Schema::hasColumn($this->getTable(), 'is_deleted')) {
            $this->is_deleted = false;
        }

        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * Scope: only return soft-deleted (archived) records.
     * Useful for admin "View Archived" pages.
     */
    public function scopeOnlyDeleted(Builder $query): Builder
    {
        return $query->withoutGlobalScope('soft_delete_filter')
            ->where($this->getTable() . '.is_deleted', true);
    }

    /**
     * Scope: return ALL records including soft-deleted ones.
     * For admin/debug views.
     */
    public function scopeWithTrashedIncludingDeleted(Builder $query): Builder
    {
        return $query->withoutGlobalScope('soft_delete_filter');
    }
}
