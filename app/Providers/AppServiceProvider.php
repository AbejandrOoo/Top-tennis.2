<?php

namespace App\Providers;

use App\Models\Cancha;
use App\Models\Tarifa;
use App\Policies\CanchaPolicy;
use App\Policies\TarifaPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        Cancha::class => CanchaPolicy::class,
        Tarifa::class => TarifaPolicy::class,
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
