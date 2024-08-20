<?php

namespace MythicalSystemsFramework\Api\Apis\System;

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Api\Apis\ApiBuilder;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class Logs extends Api implements ApiBuilder
{
    public string $route = '/system/logs';

    public string $description = 'This route will just return the logs';

    public function handleRequest(): void
    {
        self::allowOnlyGET();
        Api::OK('Showing you latest logs', [
            'logs' => Logger::getAllSortedByDate(LoggerTypes::OTHER, LoggerLevels::OTHER, 10),
        ]);
    }
}
