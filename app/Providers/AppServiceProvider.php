<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\SuCo;
use App\Observers\SuCoObserver;
use App\Models\Khu;
use App\Models\Phong;
use App\Observers\KhuObserver;
use App\Observers\PhongObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        SuCo::observe(SuCoObserver::class);
        Khu::observe(KhuObserver::class);
        Phong::observe(PhongObserver::class);
    }
}
