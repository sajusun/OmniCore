<?php

namespace App\Models;

use App\Traits\HasPost;
use App\Traits\HasNotifications;
use App\Modules\Social\Traits\HasSocialRelations;
use App\Modules\Media\Traits\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory,
        HasSocialRelations,
        HasMedia,
        HasNotifications,
        HasPost,
        HasRoles,
        Notifiable,
        SoftDeletes;

    protected $guard_name = 'web';

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'name',
        'username',
        'avatar',
        'email',
        'password',
        'is_subscribed',
        'subscription_ends_at',
        'last_activity_at',
        'remember_token',
        'slug',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        // 'role',
        'is_online',
    ];

    protected function casts(): array
    {
        return [
            'otp_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_at' => 'datetime',
            'is_subscribed' => 'boolean',
            'subscription_ends_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::created(function ($user) {
            $user->profile()->create();
        });
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(Verification::class);
    }

    public function isEmailVerified(): bool
    {
        return $this->verifications()->where('purpose', Verification::PURPOSE_EMAIL_VERIFICATION)
            ->where('status', Verification::STATUS_VERIFIED)->whereNotNull('verified_at')->exists();
    }

    public function firebaseTokens(): HasMany
    {
        return $this->hasMany(FirebaseToken::class);
    }

    public function activeFirebaseTokens(): HasMany
    {
        return $this->hasMany(FirebaseToken::class)
            ->where('status', 'active');
    }

    public function getAvatarAttribute($value): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        // Check if the request is an API request
        if (request()->is('api/*') && ! empty($value)) {
            // Return the full URL for API requests
            return url($value);
        }

        // Return only the path for web requests
        return $value;
    }

    public function getIsOnlineAttribute()
    {
        return $this->last_activity_at > now()->subMinutes(5);
    }

    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first();
    }

    /**
     * Check if the user is a Super Admin.
     * Super Admins bypass all permission checks via RolePermissionMiddleware.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if the user holds any admin-level role.
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    public function garage()
    {
        return $this->hasOne(Garage::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public function clubs()
    {
        return $this->hasMany(Club::class, 'created_by');
    }



    /*
     |--------------------------------------------------------------------------
     | Chat Relationships
     |--------------------------------------------------------------------------
     */

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(ChatRoom::class, 'chat_participants', 'user_id', 'chat_room_id')
            ->withPivot(['role', 'joined_at', 'last_read_message_id', 'last_read_at', 'notification_enabled', 'sound_enabled', 'mute_until', 'settings'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function eventBookmarks(): HasMany
    {
        return $this->hasMany(EventBookmark::class);
    }
}
