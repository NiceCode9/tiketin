<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Force HTTPS if APP_URL starts with https
        if (str_starts_with(config('app.url'), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register policies
        // ...
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\Order::class, \App\Policies\OrderPolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\Ticket::class, \App\Policies\TicketPolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\PromoCode::class, \App\Policies\PromoCodePolicy::class);
        // \Illuminate\Support\Facades\Gate::policy(\App\Models\Wristband::class, \App\Policies\WristbandPolicy::class);

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        // \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
        //     return $user->hasRole('super_admin') ? true : null;
        // });
    }
}
