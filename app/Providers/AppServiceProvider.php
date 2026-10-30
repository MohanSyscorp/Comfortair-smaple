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
        \Illuminate\Support\Facades\Route::model('gallery', \App\Models\GalleryItem::class);

        \Illuminate\Support\Facades\View::composer('layouts.admin', function ($view) {
            $count = 0;
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasModuleAccess('enquiries')) {
                $count = \App\Models\Enquiry::where('status', 'New')->count();
            }
            $view->with('newEnquiryCount', $count);
        });
    }
}
