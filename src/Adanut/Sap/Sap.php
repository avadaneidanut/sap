<?php

namespace Adanut\Sap;

use Adanut\Sap\Communication\Connection;
use Adanut\Sap\Communication\Server;
use Illuminate\Support\Facades\Config;

class Sap
{   
    /**
     * @var Connection
     */
    private $connection;

    /**
     * Open a connection to specfied server using type
     * appropriate method.
     * 
     * @param  string|Server $Server
     * @return boolean
     */
    public function open($server)
    {
        if ($server instanceof Server) {
            $this->connection = new Connection($server);
        } else {
            $this->connection = new Connection(
                Config::get('sap.' . $server)
            );
        }
    }

    /**
     * Terminate any active connection.
     * 
     * @return void
     */
    public function close()
    {

    }
}