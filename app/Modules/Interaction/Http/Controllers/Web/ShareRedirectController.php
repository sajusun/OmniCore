<?php

namespace App\Modules\Interaction\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Modules\Interaction\Services\ShareService;

class ShareRedirectController extends Controller
{
    public function __construct(
        protected ShareService $shareService
    ) {}

    /**
     * Resolve private token share link.
     */
    public function resolvePrivate(string $token)
    {
        $shareLink = $this->shareService->resolvePrivateToken($token);

        if (! $shareLink) {
            abort(410, 'This share link has expired, reached its click limit, or is no longer valid.');
        }

        $model = $shareLink->shareable;

        return view('interaction::share-preview', [
            'model' => $model,
            'shareLink' => $shareLink,
            'isPrivate' => true,
        ]);
    }

    /**
     * Resolve public SEO share link.
     */
    public function resolvePublic(string $type, string $slug)
    {
        $model = $this->shareService->resolvePublicSlug($type, $slug);

        if (! $model) {
            abort(404, 'Shared item not found.');
        }

        return view('interaction::share-preview', [
            'model' => $model,
            'shareLink' => null,
            'isPrivate' => false,
        ]);
    }
}
