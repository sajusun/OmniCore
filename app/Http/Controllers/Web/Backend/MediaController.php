<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\Media;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Laravel\Reverb\Loggers\Log;
use App\Http\Controllers\Controller;

class MediaController extends Controller
{
    public function updateStatus(Request $request, $id)
    {
        try {
            $slider = Media::findOrFail($id);

            $slider->status = $request->status;
            $slider->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error(' Status Update Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    // Delete media
    public function mediaDelete($id)
    {
        try {
            $media = Media::findOrFail($id);

            if ($media->name) {
                Helper::fileDelete($media->name);
            }

            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delete successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Slider Delete Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete slider.'
            ], 500);
        }
    }
    public function updateOrder(Request $request)
    {
        try {
            $orders = $request->orders;

            foreach ($orders as $order) {
                Media::where('id', $order['id'])->update(['sort_order' => $order['position']]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully!'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Slider Order Update Failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order.'
            ], 500);
        }
    }
}
