<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());

    Artisan::command('serve', function () {
    passthru('npm run dev');
});
})->purpose('Display an inspiring quote');

Schedule::command('citas:generar-recordatorios')->everyMinute();