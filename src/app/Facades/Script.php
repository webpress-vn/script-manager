<?php

namespace VCComponent\Laravel\Script\Facades;

use Illuminate\Support\Facades\Facade;

class Script extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'script';
    }
}
