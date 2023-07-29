<?php

namespace ConfrariaWeb\File\Providers;

use Illuminate\Support\ServiceProvider;

class FileServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../databases/Migrations');
        $this->publishes([__DIR__ . '/../../config/cw_file.php' => config_path('cw_file.php')], 'config');
    }

    public function register()
    {
    }
}
