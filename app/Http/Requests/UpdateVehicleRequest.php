<?php

namespace App\Http\Requests;

use App\Enums\BrandEnum;
use App\Enums\DriveTypeEnum;
use App\Enums\VehicleTypeEnum;
use App\Enums\TransmissionEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'garage_id' => ['nullable', 'exists:garages,id'],

            'name' => ['nullable', 'string', 'max:255'],

            'brand' => ['nullable', new Enum(BrandEnum::class)],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'digits:4', 'integer'],

            'vehicle_type' => ['nullable', new Enum(VehicleTypeEnum::class)],
            'transmission' => ['nullable', new Enum(TransmissionEnum::class)],
            'drive_type' => ['nullable', new Enum(DriveTypeEnum::class)],

            'horsepower' => ['nullable', 'integer', 'min:0'],
            'mileage' => ['nullable', 'integer', 'min:0'],

            'engine' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:100'],
            'vin' => ['nullable', 'string', 'max:255', 'unique:vehicles,vin'],

            'performance_mods' => ['nullable', 'array'],
            'performance_mods.*' => ['string', 'max:255'],

            'exterior_mods' => ['nullable', 'array'],
            'exterior_mods.*' => ['string', 'max:255'],

            'suspension' => ['nullable', 'array'],
            'suspension.*' => ['string', 'max:255'],

            'build_story' => ['nullable', 'string'],


            'media' => ['nullable', 'array'],
            'media.*' => ['file', 'mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/quicktime,video/x-msvideo', 'max:51200'],

            'parts' => ['nullable', 'array'],
            'parts.*.id' => ['nullable', 'integer', 'exists:vehicle_parts,id'],
            'parts.*.name' => ['nullable', 'string', 'max:255'],
            'parts.*.image' => ['nullable', 'file', 'image', 'max:10240'],
        ];
    }
}