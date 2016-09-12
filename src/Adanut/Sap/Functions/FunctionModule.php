<?php

namespace Adanut\Sap\Functions;

use Adanut\Sap\Communication\Connection;
use Adanut\Sap\Exceptions\FunctionCallException;
use Adanut\Sap\Exceptions\FunctionModuleParameterBindException;

class FunctionModule
{
    /**
     * Name of the function module.
     * 
     * @var string
     */
    public $name;

    /**
     * Connection of the function module.
     * 
     * @var Connection
     */
    public $connection; 

    /**
     * FunctionModule parameters
     * 
     * @var array
     */
    protected $parameters = [];

    /**
     * @var sapnwrfc_function|SAPNWRFC\RemoteFunction
     */
    protected $handle;

    /**
     * Description of the function module.
     * 
     * @var \Illuminate\Support\Collection
     */
    protected $description;
    /**
     * Create a new instance of the object.
     * 
     * @param Connection $connection
     * @param string     $name
     * @return void
     */
    public function __construct(Connection $connection, $name)
    {
        $this->connection = $connection;
        $this->name = $name;
        $this->initialize();
    }

    /**
     * Execute the function module.
     * 
     * @return mixed
     */
    public function execute()
    {
        return $this->safe(function () {
           return $this->handle->invoke($this->parameters); 
        });
    }

    /**
     * Add a parameter to the function module. 
     * 
     * @param  string $name
     * @param  mixed $value
     * @return $this    
     */
    public function param($name, $value)
    {
        // Perform parameter exists check.
        if (!$this->description->has($name)) {
            throw new FunctionModuleParameterBindException(
                sprintf(
                    'Function module parameter (%s) not found. Available parameters: %s.',
                    $name,
                    $this->description->keys()->implode(', ')
                )
            );
        }

        // Perform parameter type check.
        $type = $this->description[$name]['type'];
        $exception = false;

        if (($type === 'RFCTYPE_TABLE' || $type === 'RFCTYPE_STRUCTURE') && !is_array($value)) {
            $type .= ' (array)';
            $exception = true;
        } elseif ($type === 'RFCTYPE_CHAR' && !is_string($value)) {
            $type .= ' (string)';
            $exception = true;
        } elseif ($type === 'RFCTYPE_BYTE' && !is_string($value)) {
            $type .= ' (binary)';
            $exception = true;
        } elseif ($type === 'RFCTYPE_INT' && !is_int($value)) {
            $type .= ' (integer)';
            $exception = true;
        } 

        if ($exception) {
            throw new FunctionModuleParameterBindException(
                sprintf(
                    'Function module parameter type mismatch. Needed %s, given %s',
                    $type,
                    gettype($value)
                )
            );   
        }

        $this->parameters[$name] = $value;
        return $this;
    }

    /**
     * Initialize the function module.
     * 
     * @return void
     */
    private function initialize()
    {
        $this->safe(function () {
            if (PHP_VERSION[0] === '5') {
                $this->handle = $this->connection
                    ->getHandle()
                    ->function_lookup($this->name);
            } elseif (PHP_VERSION[0] === '7') {
                $this->handle = $this->connection
                    ->getHandle()
                    ->getFunction($this->name);
            }
            // Save the description.
            $this->description = collect(
                json_decode(
                    json_encode($this->handle),
                    true
                )
            )->except('name');
        });
    }

    /**
     * Wrap a callable with mixed version exception handle.
     * 
     * @param  callable $callback
     * 
     * @return mixed
     */
    private function safe(callable $callback)
    {
        try {
            return $callback();
        }
        catch (\Exception $e) {
            throw new FunctionCallException($e);
        }
        catch (\RuntimeException $e) {
            throw new FunctionCallException($e);
        }
    }
}
