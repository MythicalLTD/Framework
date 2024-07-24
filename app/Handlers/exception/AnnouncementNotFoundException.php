<?php

namespace MythicalSystemsFramework\Handlers\exception;

class AnnouncementNotFoundException extends \Exception
{
    public function __construct($message = "Announcement not found.", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
