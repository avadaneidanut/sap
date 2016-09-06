<?php

namespace Adanut\Sap\Facades;

use Illuminate\Support\Facades\Facade;

class Sap extends Facade
{
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sap';
    }
}
