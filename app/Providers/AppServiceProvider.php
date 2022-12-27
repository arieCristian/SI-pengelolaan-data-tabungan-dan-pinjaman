<?php

namespace App\Providers;
use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
        // Blade::directive('rupiah', function ($angka) {
        //     $format_rupiah = "Rp " . number_format($angka,2,',','.');
        //     return $format_rupiah;
        // }); 
        // Blade::directive('rupiah', function ($expression) { 
        //     return "Rp.". number_format($expression, 2, ',', '.');
        //  });
    

        Gate::define('admin', function(User $user){
            return $user->role === 'admin';
        });
        Gate::define('kolektor', function(User $user){
            return $user->role === 'kolektor';
        });
        Gate::define('administrasi', function(User $user){
            return $user->role === 'administrasi';
        });
        Gate::define('nasabah', function(User $user){
            return $user->role === 'nasabah';
        });
        Gate::define('kasir', function(User $user){
            return $user->role === 'kasir';
        });
    }
}
