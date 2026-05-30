<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Schema;

abstract class Controller
{
    use AuthorizesRequests;

    /**
     * Soft-delete a model by setting is_deleted = true and deleted_at = now().
     * Both columns are kept in sync.
     */
    protected function softDelete(Model $model): bool
    {
        if (Schema::hasColumn($model->getTable(), 'is_deleted')) {
            $model->is_deleted = true;
        }
        $model->deleted_at = now();
        $model->save();

        return true;
    }

    /**
     * Restore a soft-deleted model by clearing is_deleted and deleted_at.
     */
    protected function restoreModel(Model $model): bool
    {
        if (Schema::hasColumn($model->getTable(), 'is_deleted')) {
            $model->is_deleted = false;
        }
        $model->deleted_at = null;
        $model->save();

        return true;
    }
}
