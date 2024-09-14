<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

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
