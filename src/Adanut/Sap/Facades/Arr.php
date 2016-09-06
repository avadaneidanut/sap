<?php

namespace Adanut\Sap\Facades;

use Illuminate\Support\Facades\Facade;

class Arr extends Facade
{
    /**
     * Return facade accessor
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'sarr';
    }
}
