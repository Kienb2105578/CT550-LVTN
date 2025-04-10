<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class LanguageComposerServiceProvider extends ServiceProvider
{

    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('backend.dashboard.layout', function ($view) {
            $languages = [
                ['code' => 'vi', 'name' => 'Tiếng Việt'],
                ['code' => 'en', 'name' => 'English']
            ];
            $view->with('languages', $languages);
        });
    }
}
