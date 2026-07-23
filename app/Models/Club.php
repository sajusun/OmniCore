<?php

namespace App\Models;

use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;
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
}