<?php

namespace App\Http\Controllers\Web\Backend\Pages;

use App\Models\CMS;
use App\Enums\PageName;
use App\Enums\SectionName;
use App\Models\PageContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Media\Traits\HandlesMedia;

class PageContentController extends Controller
{
    use HandlesMedia;
    public function edit(string $page, string $section)
    {
        $pageEnum    = PageName::tryFrom($page);
        $sectionEnum = SectionName::tryFrom($section);

        abort_if(! $pageEnum || ! $sectionEnum, 404);
        abort_if(! in_array($sectionEnum, $pageEnum->sections()), 404);

        $data     = CMS::with([
            'media' => function ($query) {
                $query->orderBy('sort_order');
            }
        ])->where('page', $page)->where('section', $section)->first();
        $elements = $sectionEnum->elements();

        return view('backend.cms.index', compact('data', 'elements', 'page', 'section'));
    }

    public function update(Request $request, string $page, string $section)
    {
        $pageEnum    = PageName::tryFrom($page);
        $sectionEnum = SectionName::tryFrom($section);

        abort_if(! $pageEnum || ! $sectionEnum, 404);
        abort_if(! in_array($sectionEnum, $pageEnum->sections()), 404);

        $payload = $request->except(['_token', '_method', 'meta_keys', 'meta_values']);

        // image handle
        if ($request->hasFile('image')) {
            $payload['image'] = $this->uploadImage($request->file('image'), $page, $section);
        }

        // bg handle
        if ($request->hasFile('bg')) {
            $payload['bg'] = $this->uploadImage($request->file('bg'), $page, $section);
        }

        // meta JSON handle: decode JSON string sent from the key-value builder
        if ($request->has('meta') && is_string($request->input('meta'))) {
            $decoded = json_decode($request->input('meta'), true);
            $payload['meta'] = is_array($decoded) ? $decoded : null;
        }

        $data = CMS::updateOrCreate(
            ['page' => $page, 'section' => $section],
            $payload
        );

        // multiple images handle
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // $path = $this->uploadImage($image, $page, $section);
                // $data->media()->create(['name' => $path, 'type' => 'image', 'status' => 'active']);
                $this->uploadMedia($data, $image, 'images', 'public');
            }
        }

        return back()->with('success', 'save successfully');
    }

    private function uploadImage($file, string $page, string $section): string
    {
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path     = "uploads/cms/{$page}/{$section}";
        $file->move(public_path($path), $filename);

        return "{$path}/{$filename}";
    }
}
