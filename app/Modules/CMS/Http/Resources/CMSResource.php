<?php

namespace App\Modules\CMS\Http\Resources;

use App\Http\Resources\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CMSResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'page'            => $this->page,
            'section'         => $this->section,
            'title'           => $this->title,
            'sub_title'       => $this->when($this->sub_title && $this->sub_title !== 'null', $this->sub_title),
            'description'     => $this->when($this->description, $this->description),
            'sub_description' => $this->when($this->sub_description, $this->sub_description),
            'image'           => $this->when($this->image && $this->image !== '', asset($this->image)),
            'media'           => $this->when($this->media && count($this->media) !== 0, MediaResource::collection($this->media)),
            'btn_text'        => $this->when($this->btn_text, $this->btn_text),
            'video_path'      => $this->when($this->video_path && $this->video_path !== '', asset($this->video_path)),
        ];
    }
}
