<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ExcelServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // We'll set properties directly in the export class
        // instead of trying to set default properties globally
    }
}
