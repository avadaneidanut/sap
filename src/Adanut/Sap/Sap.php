<?php

namespace Adanut\Sap;

use Illuminate\Support\Facades\Config;
use Adanut\Sap\Communication\Server;
use Adanut\Sap\Communication\Connection;
use Adanut\Sap\Exceptions\ConnectionException;

class Sap
{   
    /**
     * Active connections.
     * 
     * @var array
     */
    protected $connections = [];

    /**
     * Return the specified connection if opened or open and return it.
     * 
     * @param  string $name
     * 
     * @return Connection
     */
    public function connection($name)
    {
        if (isset($this->connections[$name])) {
            if ($this->connections[$name]->ping()) {
                return $this->connections[$name];
            }
        }
        return $this->open($name);
    }

    /**
     * Open a connection to specfied server using type
     * appropriate method.
     * 
     * @param  string $name
     * @return boolean
     */
    public function open($name)
    {
        $this->connections[$name] = new Connection(
            new Server($this->config($name))
        );

        return $this->connections[$name];
    }

    /**
     * Terminate any active connection.
     * 
     * @return void
     */
    public function close()
    {
        foreach ($this->connections as $connection) {
            $connection->close();
        }
    }

    /**
     * Test all stored connections in the configuration.
     * 
     * @return void
     */
    public function testConfig()
    {
        $report = [];

        foreach (array_keys(Config::get('sap.connections')) as $name) {
            try {
                $this->open($name);
            } catch (ConnectionException $e) {
                $report[$name] = false;
                continue;
            }
            $report[$name] = true;
        }

        return $report;
    }

    /**
     * Get the config for specified connection.
     * 
     * @param  string $name
     * 
     * @return array
     */
    private function config($name)
    {
        $config = Config::get('sap.connections.' . $name);

        if (!$config) {
            throw new ConfiguratioNotFoundException($name);
        }

        return $config;
    }
}