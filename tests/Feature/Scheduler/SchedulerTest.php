<?php

namespace Tests\Feature\Scheduler;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduler_contains_properly_configured_intervals(): void
    {
        $schedule = app(Schedule::class);

        $events = collect($schedule->events());

        $hasUptimeOrIntegrityCallbacks = $events->contains(function (Event $event) {
            return str_contains($event->expression, '*/5') || str_contains($event->expression, '0 */2');
        });

        $this->assertTrue($hasUptimeOrIntegrityCallbacks);
    }
}
