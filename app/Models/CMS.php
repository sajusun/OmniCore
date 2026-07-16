<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CMS extends Model
{
    protected $table = "cms";

    protected $fillable = [
        'page',
        'section',
        'name',
        'title',
        'subtitle',
        'description',
        'short_description',
        'image',
        'bg',
        'video',
        'meta',
        'status'
    ];

    protected $casts = [
        'meta'    => 'array',
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
