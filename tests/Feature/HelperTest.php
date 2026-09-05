<?php

namespace Tests\Feature;

use App\Helpers\ApiResponse;
use App\Helpers\Helper;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HelperTest extends TestCase
{
    use DatabaseTransactions;

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

    public function test_api_response_structure_and_methods(): void
    {
        // 1. Test ApiResponse::success
        $res = ApiResponse::success(['id' => 1, 'name' => 'Test'], 'Success message', 200);
        $data = $res->getData(true);
        $this->assertTrue($data['status']);
        $this->assertEquals(200, $data['code']);
        $this->assertEquals('Success message', $data['message']);
        $this->assertEquals(['id' => 1, 'name' => 'Test'], $data['data']);

        // 2. Test ApiResponse::created
        $createdRes = ApiResponse::created(['id' => 2], 'Resource created');
        $this->assertEquals(201, $createdRes->getStatusCode());
        $this->assertTrue($createdRes->getData(true)['status']);

        // 3. Test ApiResponse::error
        $errRes = ApiResponse::error('Validation error occurred', 422, ['email' => ['Invalid email']]);
        $errData = $errRes->getData(true);
        $this->assertFalse($errData['status']);
        $this->assertEquals(422, $errData['code']);
        $this->assertArrayHasKey('email', $errData['errors']);

        // 4. Test Global helper functions
        $helperSuccess = api_success(['sample' => 'data'], 'Helper success');
        $this->assertTrue($helperSuccess->getData(true)['status']);

        $helperError = api_error('Helper error', 400);
        $this->assertFalse($helperError->getData(true)['status']);
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

        // 1. Test Standard Offset Pagination via ApiResponse::paginated
        $offsetPaginator = User::paginate(2);
        $offsetResponse = ApiResponse::paginated($offsetPaginator, null, 'Offset fetched');
        $offsetData = $offsetResponse->getData(true);

        $this->assertTrue($offsetData['status']);
        $this->assertArrayHasKey('pagination', $offsetData);
        $this->assertEquals('offset', $offsetData['pagination']['type']);
        $this->assertEquals(1, $offsetData['pagination']['current_page']);
        $this->assertEquals(2, $offsetData['pagination']['per_page']);

        // 2. Test Cursor Pagination via ApiResponse::paginated
        $cursorPaginator = User::orderBy('id', 'desc')->cursorPaginate(2);
        $cursorResponse = ApiResponse::paginated($cursorPaginator, null, 'Cursor feed fetched');
        $cursorData = $cursorResponse->getData(true);

        $this->assertTrue($cursorData['status']);
        $this->assertArrayHasKey('pagination', $cursorData);
        $this->assertEquals('cursor', $cursorData['pagination']['type']);
        $this->assertEquals(2, $cursorData['pagination']['per_page']);
        $this->assertArrayHasKey('has_more', $cursorData['pagination']);
    }
}
