<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    

    public function register()
    {
        $helpers = app_path('helpers.php');
        if (is_file($helpers)) {
            require_once $helpers;
        }
    }

    

    public function boot()
    {
        Paginator::useBootstrap();
        if(session('booking_completed')){
            session()->flush();
            return redirect()->route('booking');
        }
    }
}
