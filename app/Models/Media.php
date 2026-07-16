<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'type',
        'collection',
        'name',
        'order',
        'path',
        'mime_type',
        'size'
    ];

    public function mediable()
    {
        return $this->morphTo();
    }
}


