<?php

namespace MythicalSystemsFramework\Database\exception\database;

use Exception;
use Throwable;

class MySQLError extends Exception
{
    public function __construct($message = "MySQL Error.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
