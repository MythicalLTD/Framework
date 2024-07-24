<?php

namespace MythicalSystemsFramework\Handlers\exception;

class NotificationNotFoundException extends \Exception
{
    public function __construct($message = "Notification not found.", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
