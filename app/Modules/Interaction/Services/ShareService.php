<?php

namespace App\Modules\Interaction\Services;

use App\Models\User;
use App\Modules\Interaction\Models\ShareLink;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ShareService
{
    /**
     * Resolve target model from type and ID.
     */
    public function resolveModel(string $type, int|string $id): Model
    {
        $morphMap = Relation::morphMap();
        $modelClass = $morphMap[$type] ?? (class_exists($type) ? $type : null);

        if (! $modelClass || ! class_exists($modelClass)) {
            throw new InvalidArgumentException("Invalid shareable type: {$type}");
        }

        $model = $modelClass::find($id);
        if (! $model) {
            throw new InvalidArgumentException("Target model not found for {$type} #{$id}");
        }

        return $model;
    }

    /**
     * Generate or retrieve a Public SEO-friendly Share Link.
     */
    public function generatePublicLink(Model $model, ?User $user = null): ShareLink
    {
        $slug = $this->resolveSeoSlug($model);

        return ShareLink::firstOrCreate(
            [
                'shareable_type' => $model->getMorphClass(),
                'shareable_id' => $model->getKey(),
                'type' => 'public',
            ],
            [
                'user_id' => $user?->id,
                'slug' => $slug,
                'is_active' => true,
            ]
        );
    }

    /**
     * Generate a Private Expiring or Single-Use Share Link.
     */
    public function generatePrivateLink(
        Model $model,
        ?User $user = null,
        ?DateTimeInterface $expiresAt = null,
        ?int $maxClicks = 1
    ): ShareLink {
        $token = 'sec_'.Str::random(32).'_'.dechex(time());
        $type = $maxClicks === 1 ? 'single_use' : ($expiresAt ? 'expiring' : 'private');

        return ShareLink::create([
            'user_id' => $user?->id,
            'shareable_type' => $model->getMorphClass(),
            'shareable_id' => $model->getKey(),
            'type' => $type,
            'token' => $token,
            'expires_at' => $expiresAt,
            'max_clicks' => $maxClicks,
            'click_count' => 0,
            'is_active' => true,
        ]);
    }

    /**
     * Validate and process a private share link access.
     */
    public function resolvePrivateToken(string $token): ?ShareLink
    {
        $shareLink = ShareLink::where('token', $token)->with('shareable')->first();

        if (! $shareLink || ! $shareLink->isValid()) {
            return null;
        }

        $shareLink->recordClick();

        return $shareLink;
    }

    /**
     * Resolve a public SEO slug to the target model and share link.
     */
    public function resolvePublicSlug(string $type, string $slug): ?Model
    {
        $morphMap = Relation::morphMap();
        $modelClass = $morphMap[$type] ?? (class_exists($type) ? $type : null);

        if (! $modelClass || ! class_exists($modelClass)) {
            return null;
        }

        // 1. Try finding via share_links table
        $shareLink = ShareLink::where('shareable_type', (new $modelClass)->getMorphClass())
            ->where('slug', $slug)
            ->where('type', 'public')
            ->where('is_active', true)
            ->first();

        if ($shareLink) {
            $shareLink->recordClick();

            return $shareLink->shareable;
        }

        // 2. Direct model query fallback (by slug or id)
        if (in_array('slug', (new $modelClass)->getFillable())) {
            $model = $modelClass::where('slug', $slug)->first();
            if ($model) {
                return $model;
            }
        }

        if (is_numeric($slug)) {
            return $modelClass::find($slug);
        }

        return null;
    }

    /**
     * Dynamic SEO Slug Resolver.
     */
    protected function resolveSeoSlug(Model $model): string
    {
        if (method_exists($model, 'getShareableSlug')) {
            return $model->getShareableSlug();
        }

        if (! empty($model->slug)) {
            return (string) $model->slug;
        }

        if (! empty($model->title)) {
            return Str::slug($model->title).'-'.$model->getKey();
        }

        if (! empty($model->name)) {
            return Str::slug($model->name).'-'.$model->getKey();
        }

        return (string) $model->getKey();
    }
}
