<?php

namespace App\Models;

use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Club extends Model
{
    use HasFactory ,HasMedia;


    protected $fillable = [
        'name',
        'type',
        'country',
        'state',
        'city',
        'description',
        'status',
        'created_by',
    ];


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function getClubTypeLabelAttribute(): string
    {
        return match($this->type) {
            'car' => 'Car Club',
            'motorcycle' => 'Motorcycle Club',
            default => $this->type ?? '',
        };
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }


    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

 
    public function isComplete(): bool
    {
        return $this->current_step >= 3;
    }

 
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset($this->cover_image);
        }
        
        return asset('images/default-club-cover.jpg');
    }

    /**
     * Get the club's thumbnail media model.
     */
    public function getThumbnailAttribute()
    {
        return $this->firstMedia('thumbnail');
    }

    /**
     * Get the club's thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->firstMedia('thumbnail')?->url;
    }

    /**
     * Get all image media models for the club.
     */
    public function getImagesAttribute()
    {
        return $this->mediaCollection('images')->get();
    }

    /**
     * Get all image URLs for the club.
     */
    public function getImagesUrlsAttribute(): array
    {
        return $this->mediaCollection('images')->get()->pluck('url')->toArray();
    }

    /**
     * Get all video media models for the club.
     */
    public function getVideosAttribute()
    {
        return $this->mediaCollection('video')->get();
    }

    /**
     * Get all video URLs for the club.
     */
    public function getVideosUrlsAttribute(): array
    {
        return $this->mediaCollection('video')->get()->pluck('url')->toArray();
    }

    // ── Membership Relationships ──────────────────────────────────────────

    /**
     * All club_members rows (any status).
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    /**
     * Users who are approved members.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_members', 'club_id', 'user_id')
            ->withPivot(['role', 'status', 'joined_at', 'approved_at', 'approved_by'])
            ->withTimestamps()
            ->wherePivot('status', 'approved');
    }

    /**
     * Pending membership requests (future admin-approval use).
     */
    public function pendingMembers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'club_members', 'club_id', 'user_id')
            ->withPivot(['role', 'status', 'joined_at', 'approved_at', 'approved_by'])
            ->withTimestamps()
            ->wherePivot('status', 'pending');
    }

    // ── Membership Helpers ────────────────────────────────────────────────

    /**
     * Is this user an approved member (or admin) of the club?
     */
    public function isMember(int $userId): bool
    {
        return $this->memberships()
            ->where('user_id', $userId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Is this user the creator of the club?
     */
    public function isCreator(int $userId): bool
    {
        return $this->created_by === $userId;
    }

    /**
     * Is this user an admin of the club (via club_members role)?
     */
    public function isMemberAdmin(int $userId): bool
    {
        return $this->memberships()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get the ClubMember row for a given user, or null.
     */
    public function getMembership(int $userId): ?ClubMember
    {
        return $this->memberships()->where('user_id', $userId)->first();
    }
}