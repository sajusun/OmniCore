<?php

namespace App\Models;

use App\Modules\Media\Traits\HasMedia;
use Illuminate\Database\Eloquent\Model;

class SavedPost extends Model
{
    use HasMedia;
    protected $guarded = [];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}