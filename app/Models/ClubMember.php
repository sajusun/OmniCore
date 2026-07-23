<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubMember extends Model
{
    protected $table = 'club_members';

    protected $fillable = [
        'club_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'joined_at'   => 'datetime',
        'approved_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin user who approved / rejected this membership.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Status helpers ────────────────────────────────────

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    // ── Role helpers ──────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }
}
