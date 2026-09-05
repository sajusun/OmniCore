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
    public function __construct()
    {
        parent::__construct();
    }

    public function page(string $page): JsonResponse
    {
        $pageEnum = PageName::tryFrom($page);

        if (!$pageEnum) {
            return $this->notFound('Page not found.');
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

        return $this->success([
            'page' => $page,
            'data' => $data,
        ], 'Page data fetched successfully.');
    }

    public function section(string $page, string $section): JsonResponse
    {
        $pageEnum    = PageName::tryFrom($page);
        $sectionEnum = SectionName::tryFrom($section);

        if (!$pageEnum || !$sectionEnum) {
            return $this->notFound('Page or section not found.');
        }

        if (!in_array($sectionEnum, $pageEnum->sections())) {
            return $this->notFound("Section '{$section}' does not belong to page '{$page}'.");
        }

        $record = CMS::with('media')
            ->where('page', $page)
            ->where('section', $section)
            ->where('status', 'active')
            ->first();

        return $this->success([
            'page'    => $page,
            'section' => $section,
            'data'    => $record ? new CMSResource($record) : null,
        ], 'Section data fetched successfully.');
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

        return $this->success($pages, 'CMS pages list fetched successfully.');
    }
}
