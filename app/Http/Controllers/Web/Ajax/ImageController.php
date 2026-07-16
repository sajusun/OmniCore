<?php

namespace App\Http\Controllers\Web\Ajax;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Image;
use Exception;

class ImageController extends Controller
{
    public function index($post_id)
    {
        $images = Image::where('post_id', $post_id)->get();
        $data = [
            'images' => $images
        ];
        return response()->json([
            'status' => 't-success',
            'message' => 'Your action was successful!',
            'data' => $data
        ]);
        
    }
    public function destroy(string $id)
    {
        try {
            $data = Image::findOrFail($id);
            if ($data->path && file_exists(public_path($data->path))) {
                Helper::fileDelete(public_path($data->path));
            }
            $data->delete();
            return response()->json([
                'post_id' => $data->post_id,
                'status' => 't-success',
                'message' => 'Your action was successful!'
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Your action was successful!'
            ]);
        }
    }

}
