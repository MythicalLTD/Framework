<?php

namespace MythicalSystemsFramework\Managers\exception\settings;

class NoMigrationsFound extends \Exception
{
    public function __construct($message = 'No migration not found.', $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
