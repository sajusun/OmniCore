<?php

namespace App\Models;

use App\Modules\Auth\Traits\HasVerification;
use App\Traits\HasPost;
use App\Modules\Notification\Traits\HasNotifications;
use App\Modules\Notification\Models\FirebaseToken;
use App\Modules\Social\Traits\HasSocialRelations;
use App\Modules\Media\Traits\HasMedia;
use App\Modules\Product\Traits\HasEcommerce;
use App\Modules\Interaction\Traits\CanInteract;
use App\Modules\Payment\Traits\HasWallet;
use App\Modules\Subscription\Traits\HasSubscriptions;
use App\Modules\Reward\Traits\HasRewards;
use App\Modules\Ticket\Traits\HasTickets;
use App\Modules\Review\Traits\CanReview;
use App\Modules\Affiliate\Traits\HasAffiliate;
use App\Modules\Vendor\Traits\HasVendorStore;
use App\Modules\AI\Traits\HasAiConversations;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory,
        HasVerification,
        HasSocialRelations,
        HasMedia,
        HasEcommerce,
        HasNotifications,
        HasWallet,
        HasSubscriptions,
        HasRewards,
        HasTickets,
        CanReview,
        HasAffiliate,
        HasVendorStore,
        HasAiConversations,
        HasPost,
        HasRoles,
        CanInteract,
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
        static::creating(function ($user) {
            if (empty($user->slug)) {
                $user->slug = static::generateUniqueSlug($user->name);
            }
        });

        static::created(function ($user) {
            $user->profile()->create();
        });
    }

    /**
     * Generate a unique slug based on user name + random string.
     */
    public static function generateUniqueSlug(?string $name = null): string
    {
        $base = Str::slug($name ?: 'user');
        if (empty($base)) {
            $base = 'user';
        }

        do {
            $slug = $base . '-' . Str::lower(Str::random(6));
        } while (static::where('slug', $slug)->exists());

        return $slug;
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
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
