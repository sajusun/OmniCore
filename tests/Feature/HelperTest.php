<?php

namespace Tests\Feature;

use App\Helpers\Helper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HelperTest extends TestCase
{
    public function test_file_upload_update_and_delete_with_storage(): void
    {
        Storage::fake('public');

        // 1. Test Upload
        $file = UploadedFile::fake()->image('test_avatar.jpg');
        $path = Helper::fileUpload($file, 'users/avatars', 'custom_avatar', 'public');

        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        // 2. Test URL generation
        $url = Helper::fileUrl($path, 'public');
        $this->assertNotNull($url);

        // 3. Test Update (upload new, delete old)
        $newFile = UploadedFile::fake()->image('new_avatar.png');
        $newPath = Helper::fileUpdate($newFile, 'users/avatars', $path, 'new_custom_avatar', 'public');

        $this->assertNotNull($newPath);
        Storage::disk('public')->assertMissing($path); // Old path deleted
        Storage::disk('public')->assertExists($newPath); // New path stored

        // 4. Test Delete
        $deleted = Helper::fileDelete($newPath, 'public');
        $this->assertTrue($deleted);
        Storage::disk('public')->assertMissing($newPath);
    }
}
