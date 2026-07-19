<?php

namespace App\Services;

use App\Models\User;
use App\Models\Garage;
use App\Helpers\Helper;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GarageService
{

    private function generateUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Garage::where('slug', $slug)->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))->exists()) {
            $slug = "{$originalSlug}-" . $count++;
        }

        return $slug;
    }


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


    public function find(int $id): Garage
    {
        return Garage::with(['user', 'vehicles'])->findOrFail($id);
    }


    public function findByUser(User $user): ?Garage
    {
        // Eloquent relation use kore fetch kora pipeline maintain kore bhalo
        return Garage::where('user_id', $user->id)->first();
    }


    public function create(array $data): Garage
    {
        // Jodi controller theke user_id pass na kora hoy, auth user use hobe
        if (!isset($data['user_id'])) {
            $data['user_id'] = Auth::id();
        }

        // Slug blank thakle auto name baseline dynamic conversion hobe
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
        }

        // Controller theke UploadedFile object ashle asset mapping execute hobe
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
            $data['banner'] = $this->uploadBanner($data['banner']);
        }

        return Garage::create($data);
    }


    public function update(Garage $garage, array $data): Garage
    {
        // Name change hole slug system automatically refresh hobe
        if (!empty($data['name']) && $data['name'] !== $garage->name && empty($data['slug'])) {
            $data['slug'] = $this->generateUniqueSlug($data['name'], $garage->id);
        }

        // Logo configuration change checking runtime tracking
        if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
            $this->deleteOldFile($garage->logo); // Purono logo delete
            $data['logo'] = $this->uploadLogo($data['logo']);
        }

        // Banner configuration change checking runtime tracking
        if (isset($data['banner']) && $data['banner'] instanceof UploadedFile) {
            $this->deleteOldFile($garage->banner); // Purono banner delete
            $data['banner'] = $this->uploadBanner($data['banner']);
        }

        $garage->update($data);

        return $garage;
    }


    public function delete(Garage $garage): bool
    {
        $this->deleteOldFile($garage->logo);
        $this->deleteOldFile($garage->banner);

        return $garage->delete();
    }

    public function uploadLogo(UploadedFile $file): string
    {
        // Apnar framework workflow onusare destination path provide kora holo
        return Helper::fileUpload($file, 'garages/logos');
    }

    public function uploadBanner(UploadedFile $file): string
    {
        return Helper::fileUpload($file, 'garages/banners');
    }
}
