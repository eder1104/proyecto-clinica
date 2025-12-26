<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Cita;
use App\Models\RecordatorioCita;
use App\Observers\CitaObserver;
use App\Observers\RecordatorioObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Cita::observe(CitaObserver::class);
        RecordatorioCita::observe(RecordatorioObserver::class);
    }
}