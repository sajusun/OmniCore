<?php

namespace App\Http\Controllers\Web\Backend;

use Exception;
use App\Models\CMS;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Media\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{


    public function index(string $page, $section = "main")
    {
        $data = CMS::where('page', $page)->where('section', $section)->first();
        return view('backend.privacy-page.index', compact('data', 'page', 'section'));
    }


    public function update(Request $request, string $page, string $section)
    {
        // $home = CMS::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'title'             => 'required|string|max:255',
            'description'       => 'nullable|string',
            // 'images.*'          => 'nullable|image|mimes:jpg,png,jpeg,webp|max:2048',
            'status'            => 'nullable|in:active,inactive',
        ]);

        // dd([$page,$section]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $data = $validator->validated();

            $cms = CMS::updateOrCreate(
                [
                    'page'    => $page,
                    'section' => $section,
                ],
                [
                    'title'       => $request->title,
                    'description' => $request->description,
                    'status'      => $request->status ?? 'active',
                ]
            );

            session()->put('t-success', 'updated successfully');
        } catch (Exception $e) {

            session()->put('t-error', $e->getMessage());
            return redirect()->back()->with('t-error', $e->getMessage());
        }

        return redirect()->back()->with('t-success', 'updated successfully');
    }



    public function status(int $id): JsonResponse
    {
        $data = CMS::findOrFail($id);
        if (!$data) {
            return response()->json([
                'status' => 't-error',
                'message' => 'Item not found.',
            ]);
        }
        $data->status = $data->status === 'active' ? 'inactive' : 'active';
        $data->save();
        return response()->json([
            'status' => 't-success',
            'message' => 'Your action was successful!',
        ]);
    }
}
