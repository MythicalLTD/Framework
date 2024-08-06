<?php

namespace MythicalSystemsFramework\Database\exception\migration;

class NoMigrationsFound extends \Exception
{
    public function __construct($message = 'No migration not found.', $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
