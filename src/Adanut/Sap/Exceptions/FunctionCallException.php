<?php

namespace Adanut\Sap\Exceptions;

use Exception;

class FunctionCallException extends Exception
{
    /**
     * Create a new instance of the object.
     * @param mixed $e
     */
    public function __construct($e)
    {
        parent::__construct($e->getMessage());
    }
}