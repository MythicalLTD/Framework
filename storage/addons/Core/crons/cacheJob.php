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

use MythicalSystemsFramework\Cache\Cache;
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQLCache;

MySQLCache::saveCache('framework_settings');
Cache::process();
echo color::translateColorsCode('Cache processed by &acrontab&r!');
