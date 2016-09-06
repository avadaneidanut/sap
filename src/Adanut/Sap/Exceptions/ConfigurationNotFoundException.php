<?php

namespace Adanut\Sap\Exceptions;

class ConfigurationNotFoundException extends \Exception
{   
    /**
     * Create a new instance of the object.
     * 
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct(
            sprintf(
                'Configuration for the %s connection not found.'
                . 'Did you add it in the config file?',
                $name
            )
        );
    }
}