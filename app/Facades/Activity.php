<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\ActivityLog log(string $event, string $module, ?\Illuminate\Database\Eloquent\Model $subject = null, ?string $description = null, array $oldValues = [], array $newValues = [], array $properties = [], ?int $userId = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog created(\Illuminate\Database\Eloquent\Model $model, ?string $description = null, ?int $userId = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog updated(\Illuminate\Database\Eloquent\Model $model, ?array $oldValues = null, ?string $description = null, ?int $userId = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog deleted(\Illuminate\Database\Eloquent\Model $model, ?string $description = null, ?int $userId = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog restored(\Illuminate\Database\Eloquent\Model $model, ?string $description = null, ?int $userId = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog login(?\Illuminate\Database\Eloquent\Model $user = null, ?string $description = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog logout(?\Illuminate\Database\Eloquent\Model $user = null, ?string $description = null, ?string $batchUuid = null)
 * @method static \App\Models\ActivityLog custom(string $event, string $module, ?\Illuminate\Database\Eloquent\Model $subject = null, ?string $description = null, array $properties = [], ?int $userId = null, ?string $batchUuid = null)
 * @method static \Illuminate\Contracts\Pagination\LengthAwarePaginator paginate(int $perPage = 20)
 * @method static \Illuminate\Support\Collection all()
 * @method static \App\Models\ActivityLog|null find(int $id)
 * @method static bool delete(\App\Models\ActivityLog $activityLog)
 * @method static bool clear()
 * @method static string startBatch(?string $uuid = null)
 * @method static void endBatch()
 * @method static string getBatchUuid()
 *
 * @see \App\Services\ActivityLogService
 */
class Activity extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \App\Services\ActivityLogService::class;
    }
}
