<?php

namespace MythicalSystemsFramework\Api\Apis\Admin;

use MythicalSystemsFramework\Api\Apis\ApiBuilder;

class Logs implements ApiBuilder
{
    public string $route = '/system/logs';

    public string $description = 'This is a more advanced route that will help you with the logs';

    public function handleRequest(): void
    {
    }
}
