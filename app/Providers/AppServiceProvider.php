<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; 
use Illuminate\Support\Facades\View;
use App\Models\Peminjaman;             
use App\Policies\PeminjamanPolicy;  
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
        if ($this->app->environment('production')) {
        \URL::forceScheme('https');
        };

        Gate::policy(Peminjaman::class, PeminjamanPolicy::class);

        Gate::before(function ($user, $ability) {
            if ($user && method_exists($user, 'hasRole')) {
                return $user->hasRole('Admin') ? true : null;
            }
        });

        Gate::define('kelola-buku', function ($user) {
            if (method_exists($user, 'hasAnyRole')) {
                return $user->hasAnyRole(['Admin', 'Petugas', 'admin', 'petugas']);
            }
            
            $role = strtolower($user->role ?? '');
            return $role === 'admin' || $role === 'petugas';
        });

        View::share('buku', []);
    }
}