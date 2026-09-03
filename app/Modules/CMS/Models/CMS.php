<?php

namespace App\Modules\CMS\Models;

use App\Modules\Media\Models\Media;
use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;

class CMS extends Model
{
    use HasMedia;

    protected $table = 'cms';

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
        'status',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
