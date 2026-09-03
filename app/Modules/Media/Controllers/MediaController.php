<?php

namespace App\Modules\Media\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Services\MediaService;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    use HandlesMedia;

    public function __construct(protected MediaService $service)
    {
        parent::__construct();
    }

    /**
     * List all media with collection & type filter.
     */
    public function index(Request $request): JsonResponse
    {
        $collection = $request->query('collection');
        $disk = $request->query('disk');
        $perPage = (int) $request->query('per_page', 20);

        $media = Media::query()
            ->when($collection, fn($q) => $q->where('collection_name', $collection))
            ->when($disk, fn($q) => $q->where('disk', $disk))
            ->latest()
            ->paginate($perPage);

        return Helper::jsonResponse(
            true,
            'Media retrieved successfully',
            200,
            $media,
            [
                'current_page' => $media->currentPage(),
                'last_page'    => $media->lastPage(),
                'total'        => $media->total(),
            ]
        );
    }

    /**
     * Show single media item details.
     */
    public function show($id): JsonResponse
    {
        $media = Media::find($id);

        if (!$media) {
            return Helper::jsonResponse(false, 'Media not found', 404);
        }

        return Helper::jsonResponse(
            true,
            'Media details retrieved successfully',
            200,
            $media
        );
    }

    /**
     * Delete one or multiple media items.
     */
    public function destroy(Request $request): JsonResponse
    {
        $ids = $request->input('ids') ?? $request->input('id');

        if (!$ids) {
            return Helper::jsonResponse(false, 'Media ID or IDs array is required', 422);
        }

        $deleted = $this->deleteMedia($ids);

        return Helper::jsonResponse(
            $deleted,
            $deleted ? 'Media deleted successfully' : 'Media not found or failed to delete',
            $deleted ? 200 : 404
        );
    }

    /**
     * Delete single media by URL parameter.
     */
    public function deleteSingle(Media $media): JsonResponse
    {
        $media->delete();

        return Helper::jsonResponse(true, 'Media deleted successfully', 200);
    }

    /**
     * Set a media item as primary in its collection.
     */
    public function setPrimary(Media $media): JsonResponse
    {
        // Unmark other media for this parent model & collection
        Media::where('mediable_type', $media->mediable_type)
            ->where('mediable_id', $media->mediable_id)
            ->where('collection_name', $media->collection_name)
            ->update(['is_primary' => false]);

        $media->update(['is_primary' => true]);

        return Helper::jsonResponse(true, 'Media set as primary successfully', 200, $media);
    }

    /**
     * Reorder media items.
     */
    public function sortOrder(Request $request): JsonResponse
    {
        $request->validate([
            'orders'   => ['required', 'array'],
            'orders.*.id' => ['required', 'integer', 'exists:media,id'],
            'orders.*.sort_order' => ['required', 'integer'],
        ]);

        foreach ($request->input('orders') as $item) {
            Media::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return Helper::jsonResponse(true, 'Media order updated successfully', 200);
    }
}
