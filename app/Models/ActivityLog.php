<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTitleDescription;

class ActivityLog extends Model
{

    use HasTitleDescription;

    protected $fillable = ['title', 'description', 'loggable_id', 'loggable_type'];

    /**
     * Get the owning loggable model.
     */
    public function loggable()
    {
        return $this->morphTo();
    }
}
