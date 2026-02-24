<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
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
        // Point the password reset email link to the Vue SPA instead of the backend URL
        ResetPassword::createUrlUsing(function ($user, string $token) {
            $base = rtrim(config('app.url'), '/');
            return $base . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
        });
    }
}
