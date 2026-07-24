<?php

namespace App\Models;

use App\Enums\EventTypeEnum;
use App\Enums\VehicleRequiredEnum;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory, HasMedia, SoftDeletes;

    protected $fillable = [
        'user_id',
        'club_id',
        'title',
        'description',
        'event_type',
        'location',
        'latitude',
        'longitude',
        'event_date',
        'event_time',
        'max_participants',
        'vehicles_required',
        'is_public',
        'status',
    ];

    protected $casts = [
        'vehicles_required' => 'array',
        'is_public'         => 'boolean',
        'event_date'        => 'date',
        'max_participants'  => 'integer',
        'latitude'          => 'float',
        'longitude'         => 'float',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function rsvps(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function goingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_rsvps')
            ->wherePivot('status', 'going')
            ->withTimestamps();
    }

    public function interestedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_rsvps')
            ->wherePivot('status', 'interested')
            ->withTimestamps();
    }

    // ── Media Accessors ────────────────────────────────────────────────────────

    public function getThumbnailAttribute()
    {
        return $this->firstMedia('thumbnail');
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->firstMedia('thumbnail')?->url;
    }

    public function getImagesAttribute()
    {
        return $this->mediaCollection('images')->get();
    }

    public function getImagesUrlsAttribute(): array
    {
        return $this->mediaCollection('images')->get()->pluck('url')->toArray();
    }

    // ── Enum Label Accessors ──────────────────────────────────────────────────

    public function getEventTypeLabelAttribute(): string
    {
        if (is_numeric($this->event_type)) {
            $enum = EventTypeEnum::tryFrom((int)$this->event_type);
            return $enum ? $enum->label() : (string)$this->event_type;
        }

        return (string)$this->event_type;
    }

    public function getVehiclesRequiredLabelsAttribute(): array
    {
        $ids = is_array($this->vehicles_required) ? $this->vehicles_required : [];
        $labels = [];

        foreach ($ids as $id) {
            $enum = VehicleRequiredEnum::tryFrom((int)$id);
            if ($enum) {
                $labels[] = [
                    'id'   => $enum->value,
                    'name' => $enum->label(),
                ];
            }
        }

        return $labels;
    }

    // ── RSVP Helpers ─────────────────────────────────────────────────────────

    public function getUserRsvpStatus(int $userId): ?string
    {
        return $this->rsvps()->where('user_id', $userId)->value('status');
    }

    public function isUserGoing(int $userId): bool
    {
        return $this->rsvps()->where('user_id', $userId)->where('status', 'going')->exists();
    }

    public function isUserInterested(int $userId): bool
    {
        return $this->rsvps()->where('user_id', $userId)->where('status', 'interested')->exists();
    }
}
