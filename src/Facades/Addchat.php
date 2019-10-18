<?php

namespace Classiebit\Addchat\Facades;

use Illuminate\Support\Facades\Facade;

class Addchat extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'addchat';
    }
}
