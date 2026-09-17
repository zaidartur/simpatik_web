<?php

namespace Tests\Unit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\ActivityLogService;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    /**
     * Test ActivityLogService writes record to database.
     */
    public function test_log_service_creates_activity_record(): void
    {
        $user = User::first();
        if ($user) {
            $this->actingAs($user);
        }

        $log = ActivityLogService::log(
            'execute_test',
            'Unit Testing',
            'Automated Unit Test Execution',
            null,
            ['initial' => 'old'],
            ['result' => 'new']
        );

        $this->assertInstanceOf(ActivityLog::class, $log);
        $this->assertEquals('Unit Testing', $log->module);
        $this->assertEquals('execute_test', $log->action);
        $this->assertEquals('Automated Unit Test Execution', $log->description);
        $this->assertDatabaseHas('activity_logs', [
            'id' => $log->id,
            'module' => 'Unit Testing',
            'action' => 'execute_test',
        ]);

        // Clean up test log
        $log->delete();
    }
}
