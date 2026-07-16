<?php

namespace App\Modules\Media\Controllers;

use Illuminate\Http\Request;
use App\Modules\Media\Models\Media;
use App\Modules\Media\Traits\HandlesMedia;
class MediaController
{
    use HandlesMedia;
    /**
     * Delete Media
     */
    public function destroy(Request $request)
    {

        $request->validate([
            'ids' => 'required',
        ]);

        $deleted = $this->deleteMedia($request->ids);

        return response()->json([
            'status' => $deleted,
            'message' => $deleted ? 'Media deleted successfully' : 'Media not found',
            'data' => null,
        ]);
    }

    /**
     * Media List
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Media retrieved successfully',
            'data' => Media::latest()->paginate(20),
        ]);
    }

    /**
     * Show Single Media
     */
    public function show($id)
    {
        $media = Media::find($id);

        if (!$media) {

            return response()->json([
                'status' => false,
                'message' => 'Media not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Media retrieved successfully',
            'data' => $media,
        ]);
    }
}
