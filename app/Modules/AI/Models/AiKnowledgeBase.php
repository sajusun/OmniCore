<?php

namespace App\Modules\AI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiKnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'ai_knowledge_bases';

    protected $fillable = [
        'category',
        'question',
        'answer',
        'keywords',
        'tags',
        'hit_count',
        'is_active',
    ];

    protected $casts = [
        'keywords' => 'array',
        'tags' => 'array',
        'hit_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
