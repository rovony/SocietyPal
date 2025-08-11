<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:rent-generate')->monthlyOn(1, '00:00');
Schedule::command('maintenance:generate')->monthlyOn(1, '00:00');
Schedule::command('app:hide-cron-job-message')->everyMinute();
Schedule::command('maintenance:schedule')->daily();
