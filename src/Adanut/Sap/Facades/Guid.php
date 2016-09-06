<?php

namespace Adanut\Sap\Facades;

use Illuminate\Support\Facades\Facade;

class Guid extends Facade
{
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'guid';
    }
}
