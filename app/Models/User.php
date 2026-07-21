<?php

namespace App\Models;

use App\Traits\HasFriends;
use App\Traits\HasFollowers;
use App\Traits\HasNotifications;
use App\Traits\HasFriendRequests;
use App\Modules\Media\Traits\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, HasMedia, HasNotifications, HasRoles, Notifiable, SoftDeletes, HasFollowers, HasFriends, HasFriendRequests;

    protected $guard_name = ['api', 'web'];

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
}
