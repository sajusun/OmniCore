<?php

namespace App\Services;

use App\Models\User;
use App\Models\Garage;
use App\Helpers\Helper;
use App\Models\Vehicle;
use Illuminate\Support\Str;
use App\Models\VehicleImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class VehicleService
{
    /**
     * Unique slug auto-generate korar private method (Vehicle name ba model layer er jonno)
     */
    private function generateUniqueSlug(string $title, ?int $exceptId = null): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (
            Vehicle::where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }

    /**
     * Absolute public path theke file delete korar single helper handler
     */
    private function deleteOldFile(?string $dbFilePath): void
    {
        if ($dbFilePath) {
            $cleanPath = str_replace(asset(''), '', $dbFilePath);
            $absolutePath = public_path($cleanPath);
            if (file_exists($absolutePath)) {
                Helper::fileDelete($absolutePath);
            }
        }
    }

    /**
     * Find a vehicle by ID with its relations
     */
    public function find(int $id): Vehicle 
    {
        return Vehicle::with(['garage', 'images', 'brand'])->findOrFail($id);
    }

    /**
     * List all vehicles belonging to a specific user (via their garage)
     */
    public function list(User $user): LengthAwarePaginator
    {
        return Vehicle::whereHas('garage', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['garage', 'images'])->latest()->paginate(15);
    }

    /**
     * Create a new vehicle linked to a user's garage
     */
    public function create(User $user, array $data): Vehicle 
    {
        // Jodi garage_id pass na kora hoy, user er primary garage track kora
        if (empty($data['garage_id'])) {
            $garage = Garage::where('user_id', $user->id)->firstOrFail();
            $data['garage_id'] = $garage->id;
        }

        // Title/Name baseline e automatic unique slug processing
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        } elseif (empty($data['slug']) && !empty($data['title'])) {
            $data['slug'] = $this->generateUniqueSlug($data['title']);
        }

        $vehicle = Vehicle::create($data);

        // Dynamic multiple image checking process
        if (!empty($data['images']) && is_array($data['images'])) {
            $this->uploadImages($vehicle, $data['images']);
        }

        return $vehicle;
    }

    /**
     * Update an existing vehicle record
     */
    public function update(Vehicle $vehicle, array $data): Vehicle 
    {
        $checkField = isset($data['name']) ? 'name' : (isset($data['title']) ? 'title' : null);
        
        if ($checkField && $data[$checkField] !== $vehicle->$checkField && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data[$checkField], $vehicle->id);
        }

        $vehicle->update($data);

        // Upload new images if provided during update step
        if (!empty($data['images']) && is_array($data['images'])) {
            $this->uploadImages($vehicle, $data['images']);
        }

        return $vehicle;
    }

    /**
     * Delete a vehicle along with all associated gallery images
     */
    public function delete(Vehicle $vehicle): bool 
    {
        // Vehicle database item clear shomoy shob sub-images safe cleanup
        if ($vehicle->images) {
            foreach ($vehicle->images as $image) {
                $this->removeImage($image->id);
            }
        }

        return $vehicle->delete();
    }

    /**
     * Upload multiple gallery images using custom upload Helper
     */
    public function uploadImages(Vehicle $vehicle, array $images): void 
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                $path = Helper::fileUpload($image, 'vehicles/gallery');
                
                // VehicleImage mapping model array injection pipeline
                $vehicle->images()->create([
                    'image_path' => $path // Appends logic handling db path record
                ]);
            }
        }
    }

    /**
     * Remove a single target image from DB and disk storage
     */
    public function removeImage(int $imageId): bool 
    {
        $image = VehicleImage::findOrFail($imageId);
        
        // Target model physical data tracing structure execution
        $this->deleteOldFile($image->getRawOriginal('image_path') ?? $image->image_path);
        
        return $image->delete();
    }

    /**
     * Advanced multidimensional search filters query pipeline
     */
    public function search(array $filters): LengthAwarePaginator
    {
        return Vehicle::query()
            ->when(!empty($filters['search']), function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            })
            ->when(!empty($filters['garage_id']), fn($q) => $q->where('garage_id', $filters['garage_id']))
            ->when(!empty($filters['brand_id']), fn($q) => $q->where('brand_id', $filters['brand_id']))
            ->when(!empty($filters['status']), fn($q) => $q->where('status', $filters['status']))
            ->with(['garage', 'images'])
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get vehicles list scoped under a specific Garage
     */
    public function byGarage(int $garageId): Collection
    {
        return Vehicle::where('garage_id', $garageId)->with('images')->latest()->get();
    }

    /**
     * Get vehicles filtered by specific Brand ID
     */
    public function byBrand(int $brandId): Collection
    {
        return Vehicle::where('brand_id', $brandId)->with(['garage', 'images'])->latest()->get();
    }

    /**
     * Get recent vehicles related to user dashboard overview context
     */
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