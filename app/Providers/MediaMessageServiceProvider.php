<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MediaMessageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\MediaMessageService\IMediaMessageService',
            'App\Services\MediaMessageService\MediaMessageService');
    }
}
