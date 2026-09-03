<?php

namespace App\Modules\CMS\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\CMS\Enums\PageName;
use App\Modules\CMS\Enums\SectionName;
use App\Modules\CMS\Http\Resources\CMSResource;
use App\Modules\CMS\Models\CMS;
use Illuminate\Http\JsonResponse;

class CmsController extends Controller
{
    public function page(string $page): JsonResponse
    {
        $pageEnum = PageName::tryFrom($page);

        if (! $pageEnum) {
            return response()->json([
                'status'  => false,
                'message' => 'Page not found.',
            ], 404);
        }

        $sections = $pageEnum->sections();

        $records = CMS::with('media')
            ->where('page', $page)
            ->where('status', 'active')
            ->whereIn('section', array_map(fn($s) => $s->value, $sections))
            ->get()
            ->keyBy('section');

        $data = collect($sections)->mapWithKeys(function (SectionName $sectionEnum) use ($records) {
            $record = $records->get($sectionEnum->value);

            return [
                $sectionEnum->value => $record ? new CMSResource($record) : null,
            ];
        });

        return response()->json([
            'status' => true,
            'page'   => $page,
            'data'   => $data,
        ]);
    }

    public function section(string $page, string $section): JsonResponse
    {
        $pageEnum    = PageName::tryFrom($page);
        $sectionEnum = SectionName::tryFrom($section);

        if (! $pageEnum || ! $sectionEnum) {
            return response()->json([
                'success' => false,
                'message' => 'Page or section not found.',
            ], 404);
        }

        if (! in_array($sectionEnum, $pageEnum->sections())) {
            return response()->json([
                'success' => false,
                'message' => "Section '{$section}' does not belong to page '{$page}'.",
            ], 404);
        }

        $record = CMS::with('media')
            ->where('page', $page)
            ->where('section', $section)
            ->where('status', 'active')
            ->first();

        if (! $record) {
            return response()->json([
                'success' => true,
                'page'    => $page,
                'section' => $section,
                'data'    => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'page'    => $page,
            'section' => $section,
            'data'    => new CMSResource($record),
        ]);
    }

    public function index(): JsonResponse
    {
        $pages = collect(PageName::cases())->map(function (PageName $pageEnum) {
            return [
                'page'     => $pageEnum->value,
                'label'    => $pageEnum->label(),
                'sections' => collect($pageEnum->sections())->map(fn($s) => $s->value)->values(),
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $pages,
        ]);
    }
}
