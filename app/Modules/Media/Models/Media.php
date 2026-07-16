<?php

namespace App\Modules\Media\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{

    protected $table = 'media';

    protected $fillable = [

        'disk',
        'collection_name',
        'original_name',
        'file_name',
        'mime_type',
        'extension',
        'size',
        'path',
        'url',
        'width',
        'height',
        'meta',
        'sort_order',
        'is_primary',
        'status',
    ];

    protected $casts = [

        'meta' => 'array',
        'is_primary' => 'boolean',
    ];
    protected $hidden = [
        'mediable_type',
        'mediable_id',
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'full_url',
    ];

    /**
     * Polymorphic Relation
     */
    public function mediable()
    {
        return $this->morphTo();
    }

    /**
     * Full URL
     */
    public function getFullUrlAttribute()
    {
        return asset($this->path);
    }
}
