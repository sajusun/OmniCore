<?php

namespace App\Modules\ActivityLog\Observers;

use Illuminate\Database\Eloquent\Model;

class ActivityObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        activity()->created($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        activity()->updated($model);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        activity()->deleted($model);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        activity()->restored($model);
    }

    /**
     * Handle the Model "forceDeleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        activity()->custom(
            'force_deleted',
            class_basename($model),
            $model,
            "Force deleted " . class_basename($model) . " #{$model->getKey()}",
            $model->toArray()
        );
    }
}
