<?php

use App\Jobs\CheckSiteIntegrityJob;
use App\Jobs\CheckSiteUptimeJob;
use App\Models\MonitoredSite;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    MonitoredSite::active()->each(function (MonitoredSite $site) {
        CheckSiteUptimeJob::dispatch($site);
    });
})->everyFiveMinutes();

Schedule::call(function () {
    MonitoredSite::active()->each(function (MonitoredSite $site) {
        CheckSiteIntegrityJob::dispatch($site);
    });
})->everyTwoHours();

Schedule::command('model:prune')->daily();
