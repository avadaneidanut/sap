<?php

namespace Adanut\Sap\Exceptions;

use Exception;

class FunctionCallException extends Exception
{
    protected $originalException;

    /**
     * Create a new instance of the object.
     * @param mixed $e
     */
    public function __construct($e)
    {
        parent::__construct($e->getMessage());
        $this->originalException = $e;
    }
    
    public function getOriginalException()
    {
        return $this->originalException;
    }
}