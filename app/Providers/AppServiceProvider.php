<?php

namespace App\Providers;

use App\Models\Cancha;
use App\Models\Horario;
use App\Models\Tarifa;
use App\Policies\CanchaPolicy;
use App\Policies\HorarioPolicy;
use App\Policies\TarifaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Cancha::class  => CanchaPolicy::class,
        Tarifa::class  => TarifaPolicy::class,
        Horario::class => HorarioPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
