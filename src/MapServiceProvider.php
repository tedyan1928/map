<?php

namespace Tyeydy\Map;

use Illuminate\Support\ServiceProvider;

class MapServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->commands([
            Commands\GeneralArea::class,
        ]);
    }
}
