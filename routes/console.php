<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('gym:check-expired-memberships')->dailyAt('00:01');
