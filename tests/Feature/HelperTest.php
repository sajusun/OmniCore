<?php

namespace Tests\Feature;

use App\Helpers\Helper;
use App\Models\User;
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

    public function test_offset_and_cursor_pagination_json_responses(): void
    {
        // Create dummy users if not present
        if (User::count() < 3) {
            User::create([
                'name' => 'Paginate User 1',
                'email' => 'p1_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
            ]);
            User::create([
                'name' => 'Paginate User 2',
                'email' => 'p2_' . uniqid() . '@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // 1. Test Standard Offset Pagination
        $offsetPaginator = User::paginate(2);
        $offsetResponse = Helper::jsonResponse(true, 'Offset fetched', 200, $offsetPaginator);
        $offsetData = $offsetResponse->getData(true);

        $this->assertEquals('offset', $offsetData['pagination_type']);
        $this->assertArrayHasKey('pagination', $offsetData);
        $this->assertEquals(1, $offsetData['pagination']['current_page']);
        $this->assertEquals(2, $offsetData['pagination']['per_page']);

        // 2. Test Cursor Pagination (for Infinite Scroll / Mobile Feed)
        $cursorPaginator = User::orderBy('id', 'desc')->cursorPaginate(2);
        $cursorResponse = Helper::jsonResponse(true, 'Cursor feed fetched', 200, $cursorPaginator);
        $cursorData = $cursorResponse->getData(true);

        $this->assertEquals('cursor', $cursorData['pagination_type']);
        $this->assertArrayHasKey('cursor', $cursorData);
        $this->assertEquals(2, $cursorData['cursor']['per_page']);
        $this->assertArrayHasKey('next_cursor', $cursorData['cursor']);
        $this->assertArrayHasKey('has_more', $cursorData['cursor']);
    }
}
