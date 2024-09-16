<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

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
