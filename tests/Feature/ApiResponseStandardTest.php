<?php

namespace Tests\Feature;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ApiResponseStandardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_api_response_envelope_standard(): void
    {
        $response = ApiResponse::success(['foo' => 'bar'], 'Operation successful', 200);
        $data = $response->getData(true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($data['status']);
        $this->assertEquals('Operation successful', $data['message']);
        $this->assertEquals(200, $data['code']);
        $this->assertEquals(['foo' => 'bar'], $data['data']);
    }

    public function test_api_response_error_standard(): void
    {
        $response = ApiResponse::error('Validation failed', 422, ['field' => ['The field is required.']]);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($data['status']);
        $this->assertEquals('Validation failed', $data['message']);
        $this->assertEquals(422, $data['code']);
        $this->assertEquals(['field' => ['The field is required.']], $data['errors']);
    }

    public function test_api_response_error_flexible_params(): void
    {
        // Test with swapped params (message, errors, code)
        $response = ApiResponse::error('Validation failed', ['field' => ['The field is required.']], 422);
        $data = $response->getData(true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertFalse($data['status']);
        $this->assertEquals('Validation failed', $data['message']);
        $this->assertEquals(422, $data['code']);
        $this->assertEquals(['field' => ['The field is required.']], $data['errors']);
    }

    public function test_api_response_paginated_standard(): void
    {
        $items = collect([
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ]);
        $paginator = new LengthAwarePaginator($items, 10, 2, 1, [
            'path'     => 'http://localhost/api/items',
            'pageName' => 'page',
        ]);

        $response = ApiResponse::paginated($paginator, null, 'Items retrieved successfully');
        $data = $response->getData(true);

        $this->assertTrue($data['status']);
        $this->assertEquals('Items retrieved successfully', $data['message']);
        $this->assertCount(2, $data['data']);
        $this->assertArrayHasKey('pagination', $data);
        $this->assertEquals(1, $data['pagination']['current_page']);
        $this->assertEquals(5, $data['pagination']['last_page']);
        $this->assertEquals(10, $data['pagination']['total']);
        $this->assertEquals(2, $data['pagination']['per_page']);
    }

    public function test_api_unauthenticated_guard_returns_standard_response(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401)
            ->assertJson([
                'status' => false,
                'code'   => 401,
            ]);
    }

    public function test_api_not_found_returns_standard_response(): void
    {
        $response = $this->getJson('/api/non-existent-endpoint-xyz');

        $response->assertStatus(404)
            ->assertJson([
                'status' => false,
                'code'   => 404,
            ]);
    }
}
