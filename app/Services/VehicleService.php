<?php

namespace App\Services;

use App\Models\User;
use App\Models\Garage;
use App\Models\Vehicle;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Modules\Media\Traits\HandlesMedia;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    use HandlesMedia;
    private function generateUniqueSlug(string $title, ?int $exceptId = null): string
    {
        $slug = Str::slug($title) . '-' . Str::random(4);
        return $slug;
    }


    public function find(int $id): Vehicle
    {
        return Vehicle::with(['garage', 'media', 'parts.media'])->findOrFail($id);
    }


    public function list(User $user): LengthAwarePaginator
    {
        return Vehicle::whereHas('garage', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['garage', 'media', 'parts.media'])->latest()->paginate(15);
    }
    public function vehicleListRSVP(User $user)
    {
        return Vehicle::select(['id', 'name', 'brand', 'year'])
            ->whereHas('garage', fn($q) => $q->where('user_id', $user->id))
            ->with('media:id,mediable_id,mediable_type,url,path')
            ->latest()
            ->get()
            ->map(function ($vehicle) {
                $vehicle->image = optional($vehicle->media->first())->url;
                unset($vehicle->media);
                return $vehicle;
            });
    }


    public function create(User $user, array $data): Vehicle
    {


        // Jodi garage_id pass na kora hoy, user er primary garage track kora
        if (empty($data['garage_id'])) {
            $garage = Garage::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => "{$user->name}'s Garage",
                    'slug' => $this->generateUniqueSlug($data['name']),
                ]
            );
            $data['garage_id'] = $garage->id;
        }


        $vehicle = $user->vehicles()->create($data);

        if (!empty($data['media']) && is_array($data['media'])) {
            $this->uploadImages($vehicle, $data['media']);
        }

        if (!empty($data['parts']) && is_array($data['parts'])) {
            $this->syncParts($vehicle, $data['parts']);
        }

        return $vehicle->load(['garage', 'media', 'parts.media']);
    }
    public function updateStatus(Vehicle $vehicle, string $status): Vehicle
    {
        $vehicle->update(['status' => $status]);
        $vehicle->save();
        return $vehicle;
    }


    public function update(Vehicle $vehicle, array $data): Vehicle
    {
        $checkField = isset($data['name']) ? 'name' : (isset($data['title']) ? 'title' : null);

        if ($checkField && $data[$checkField] !== $vehicle->$checkField && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data[$checkField], $vehicle->id);
        }

        $vehicle->update($data);

        // Upload new media if provided during update step
        if (!empty($data['media'])) {
            $this->uploadImages($vehicle, $data['media']);
        }

        if (isset($data['parts']) && is_array($data['parts'])) {
            $this->syncParts($vehicle, $data['parts']);
        }

        return $vehicle->load(['garage', 'media', 'parts.media']);
    }

    public function syncParts(Vehicle $vehicle, array $partsData): void
    {
        foreach ($partsData as $partData) {
            if (empty($partData['name']) && empty($partData['image'])) {
                continue;
            }

            if (!empty($partData['id'])) {
                $part = $vehicle->parts()->find($partData['id']);
                if ($part) {
                    if (isset($partData['name'])) {
                        $part->update(['name' => $partData['name']]);
                    }
                }
            } else {
                $part = $vehicle->parts()->create([
                    'name' => $partData['name'] ?? null,
                ]);
            }

            if ($part && !empty($partData['image'])) {
                $this->updateMedia($part, $partData['image'], 'part_image');
            }
        }
    }


    public function delete(Vehicle $vehicle): bool
    {
        $mediaIds = $vehicle->media()->pluck('id')->toArray();
        if (!empty($mediaIds)) {
            $this->deleteMedia($mediaIds);
        }
        return $vehicle->delete();
    }


    public function uploadImages(Vehicle $vehicle, array $images): void
    {
        $this->uploadMedia($vehicle, $images, 'Vehicle', 'public');
    }


    public function removeImage(int $imageId): bool
    {
        return $this->deleteMedia($imageId);
    }


    public function search(array $filters): LengthAwarePaginator
    {
        return Vehicle::query()
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('build_story', 'like', '%' . $filters['search'] . '%');
            })->when(!empty($filters['garage_id']), fn($q) => $q->where('garage_id', $filters['garage_id']))
            ->when(!empty($filters['brand_id']), fn($q) => $q->where('brand_id', $filters['brand_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->with(['garage', 'media', 'parts.media'])->where('status', 'public')
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }


    public function byGarage(int $garageId): Collection
    {
        return Vehicle::where('garage_id', $garageId)->with('images')->latest()->get();
    }


    public function byBrand(int $brandId): Collection
    {
        return Vehicle::where('brand_id', $brandId)->with(['garage', 'images'])->latest()->get();
    }

    public function recent(User $user, int $limit = 5): Collection
    {
        return Vehicle::whereHas('garage', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['garage'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
