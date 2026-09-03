<?php

namespace App\Modules\ActivityLog\Services;

use App\Modules\ActivityLog\Models\ActivityLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogService
{
    /**
     * Active batch UUID for the current lifecycle.
     */
    protected ?string $batchUuid = null;

    /**
     * Start a new logging batch with a specific or generated UUID.
     */
    public function startBatch(?string $uuid = null): string
    {
        $this->batchUuid = $uuid ?? (string) Str::uuid();
        return $this->batchUuid;
    }

    /**
     * Clear/end the active logging batch.
     */
    public function endBatch(): void
    {
        $this->batchUuid = null;
    }

    /**
     * Get the active batch UUID, lazy-initializing it if none is set.
     */
    public function getBatchUuid(): string
    {
        if (!$this->batchUuid) {
            $this->batchUuid = (string) Str::uuid();
        }
        return $this->batchUuid;
    }

    /**
     * Paginate the activity logs with optional filters and user scoping.
     */
    public function paginate(int $perPage = 15, array $filters = [], ?int $userId = null): LengthAwarePaginator
    {
        $query = ActivityLog::with('user', 'subject')->latest();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $query = $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Paginate the activity logs belonging to a specific user.
     */
    public function paginateByUser($user, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $userId = $user instanceof Model ? $user->getKey() : (int) $user;
        return $this->paginate($perPage, $filters, $userId);
    }

    /**
     * Apply search and filter constraints to the query.
     */
    public function applyFilters($query, array $filters)
    {
        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('event', 'like', "%{$search}%")
                    ->orWhere('module', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by Event
        if (!empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        // Filter by Module
        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        // Filter by Date Range
        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    /**
     * Get unique event values in logs for filter options.
     */
    public function getUniqueEvents(): Collection
    {
        return ActivityLog::distinct()->whereNotNull('event')->orderBy('event')->pluck('event', 'event');
    }

    /**
     * Get unique module values in logs for filter options.
     */
    public function getUniqueModules(): Collection
    {
        return ActivityLog::distinct()->whereNotNull('module')->orderBy('module')->pluck('module', 'module');
    }

    /**
     * Get details of a specific log with eager loading.
     */
    public function getDetails(ActivityLog $activityLog): ActivityLog
    {
        return $activityLog->load('user', 'subject');
    }

    /**
     * Get all activity logs.
     */
    public function all(): Collection
    {
        return ActivityLog::with('user', 'subject')
            ->latest()
            ->get();
    }

    /**
     * Find a specific activity log.
     */
    public function find(int $id): ?ActivityLog
    {
        return ActivityLog::with('user', 'subject')->find($id);
    }

    /**
     * Delete an activity log record.
     */
    public function delete(ActivityLog $activityLog): bool
    {
        return (bool) $activityLog->delete();
    }

    /**
     * Clear all activity logs.
     */
    public function clear(): bool
    {
        ActivityLog::truncate();
        return true;
    }

    /**
     * Master logging method that collects request metadata and writes to the DB.
     */
    public function log(
        string $event,
        string $module,
        ?Model $subject = null,
        ?string $description = null,
        array $oldValues = [],
        array $newValues = [],
        array $properties = [],
        ?int $userId = null,
        ?string $batchUuid = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id'      => $userId ?? Auth::id(),
            'event'        => $event,
            'module'       => $module,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id'   => $subject?->getKey(),
            'description'  => $description,
            'old_values'   => empty($oldValues) ? null : $oldValues,
            'new_values'   => empty($newValues) ? null : $newValues,
            'properties'   => empty($properties) ? null : $properties,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'url'          => request()->fullUrl(),
            'method'       => request()->method(),
            'batch_uuid'   => $batchUuid ?? $this->getBatchUuid(),
        ]);
    }

    /**
     * Log a created event for a model.
     */
    public function created(Model $model, ?string $description = null, ?int $userId = null, ?string $batchUuid = null): ActivityLog
    {
        $description = $description ?? "Created " . class_basename($model) . " #{$model->getKey()}";

        return $this->log(
            'created',
            class_basename($model),
            $model,
            $description,
            [],
            $model->toArray(),
            [],
            $userId,
            $batchUuid
        );
    }

    /**
     * Log an updated event for a model, capturing changes.
     */
    public function updated(Model $model, ?array $oldValues = null, ?string $description = null, ?int $userId = null, ?string $batchUuid = null): ActivityLog
    {
        $changes = $model->getChanges();
        if (empty($changes)) {
            $changes = $model->getDirty();
        }

        $old = [];
        $new = [];

        foreach ($changes as $key => $newValue) {
            if (in_array($key, ['updated_at'])) {
                continue;
            }
            $old[$key] = $oldValues && array_key_exists($key, $oldValues)
                ? $oldValues[$key]
                : $model->getOriginal($key);
            $new[$key] = $newValue;
        }

        if (empty($old) && !empty($oldValues)) {
            $old = $oldValues;
            $new = $model->only(array_keys($oldValues));
        }

        $description = $description ?? "Updated " . class_basename($model) . " #{$model->getKey()}";

        return $this->log(
            'updated',
            class_basename($model),
            $model,
            $description,
            $old,
            $new,
            [],
            $userId,
            $batchUuid
        );
    }

    /**
     * Log a deleted event for a model.
     */
    public function deleted(Model $model, ?string $description = null, ?int $userId = null, ?string $batchUuid = null): ActivityLog
    {
        $description = $description ?? "Deleted " . class_basename($model) . " #{$model->getKey()}";

        return $this->log(
            'deleted',
            class_basename($model),
            $model,
            $description,
            $model->toArray(),
            [],
            [],
            $userId,
            $batchUuid
        );
    }

    /**
     * Log a restored event for a soft-deleted model.
     */
    public function restored(Model $model, ?string $description = null, ?int $userId = null, ?string $batchUuid = null): ActivityLog
    {
        $description = $description ?? "Restored " . class_basename($model) . " #{$model->getKey()}";

        return $this->log(
            'restored',
            class_basename($model),
            $model,
            $description,
            [],
            $model->toArray(),
            [],
            $userId,
            $batchUuid
        );
    }

    /**
     * Log a login event.
     */
    public function login(?Model $user = null, ?string $description = null, ?string $batchUuid = null): ActivityLog
    {
        $resolvedUser = $user ?? Auth::user();
        $userId = $resolvedUser?->getKey();
        $description = $description ?? "User " . ($resolvedUser?->name ?? 'Unknown') . " logged in";

        return $this->log(
            'login',
            'Auth',
            $resolvedUser,
            $description,
            [],
            [],
            [],
            $userId,
            $batchUuid
        );
    }

    /**
     * Log a logout event.
     */
    public function logout(?Model $user = null, ?string $description = null, ?string $batchUuid = null): ActivityLog
    {
        $resolvedUser = $user ?? Auth::user();
        $userId = $resolvedUser?->getKey();
        $description = $description ?? "User " . ($resolvedUser?->name ?? 'Unknown') . " logged out";

        return $this->log(
            'logout',
            'Auth',
            $resolvedUser,
            $description,
            [],
            [],
            [],
            $userId,
            $batchUuid
        );
    }

    /**
     * Log a custom event.
     */
    public function custom(
        string $event,
        string $module,
        ?Model $subject = null,
        ?string $description = null,
        array $properties = [],
        ?int $userId = null,
        ?string $batchUuid = null
    ): ActivityLog {
        return $this->log(
            $event,
            $module,
            $subject,
            $description,
            [],
            [],
            $properties,
            $userId,
            $batchUuid
        );
    }
}
