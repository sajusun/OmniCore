<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\ActivityLog\Models\ActivityLog;
use App\Modules\ActivityLog\Observers\ActivityObserver;
use App\Modules\ActivityLog\Services\ActivityLogService;
use App\Modules\ActivityLog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    /**
     * Test activity helper returns ActivityLogService instance.
     */
    public function test_activity_helper_returns_service_instance(): void
    {
        $service = activity();
        $this->assertInstanceOf(ActivityLogService::class, $service);
    }

    /**
     * Test custom activity logging works.
     */
    public function test_custom_activity_log_creation(): void
    {
        $log = activity()->custom('custom_test', 'TestModule', null, 'Testing custom activity log');

        $this->assertInstanceOf(ActivityLog::class, $log);
        $this->assertEquals('custom_test', $log->event);
        $this->assertEquals('TestModule', $log->module);
        $this->assertEquals('Testing custom activity log', $log->description);
    }

    /**
     * Test LogsActivity trait triggers observer on model lifecycle.
     */
    public function test_logs_activity_trait_tracks_model_events(): void
    {
        $dummyUser = User::first() ?? User::create([
            'name' => 'Test Activity User',
            'email' => 'activity_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
        ]);

        // Test created logging
        $logCreated = activity()->created($dummyUser, 'User created test');
        $this->assertInstanceOf(ActivityLog::class, $logCreated);
        $this->assertEquals('created', $logCreated->event);
        $this->assertEquals(User::class, $logCreated->subject_type);
        $this->assertEquals($dummyUser->id, $logCreated->subject_id);

        // Test updated logging
        $dummyUser->name = $dummyUser->name . ' Updated';
        $logUpdated = activity()->updated($dummyUser, ['name' => 'Old Name'], 'User updated test');
        $this->assertInstanceOf(ActivityLog::class, $logUpdated);
        $this->assertEquals('updated', $logUpdated->event);
    }

    /**
     * Test activity log service pagination and filtering.
     */
    public function test_activity_log_service_pagination(): void
    {
        /** @var ActivityLogService $service */
        $service = app(ActivityLogService::class);
        $paginator = $service->paginate(10);

        $this->assertNotNull($paginator);
        $this->assertGreaterThanOrEqual(1, $paginator->total());
    }
}
