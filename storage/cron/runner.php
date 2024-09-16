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

try {
    if (file_exists(__DIR__ . '/../caches/vendor/autoload.php')) {
        require __DIR__ . '/../caches/vendor/autoload.php';
    } else {
        exit('Hello, it looks like you did not run: "composer install --no-dev --optimize-autoloader". Please run that and refresh the page');
    }
} catch (Exception $e) {
    exit('Hello, it looks like you did not run: composer install --no-dev --optimize-autoloader Please run that and refresh');
}
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Plugins\PluginEvent;
use MythicalSystemsFramework\Plugins\PluginsManager;

$event = new PluginEvent();
global $event;
echo color::translateColorsCode('A new cron runner has &astarted&r.&o');
/*
 * MythicalSystems Framework Cron File
 *
 * This is the main file that adds crons to our framework
 *
 * Please do not edit anything from here and only add files inside: jobs
 */

try {
    date_default_timezone_set(Settings::getSetting('app', 'timezone'));
} catch (Exception $e) {
    date_default_timezone_set('UTC');
}

$jobsDirectory = __DIR__ . '/php';
$files = scandir($jobsDirectory);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        require $jobsDirectory . '/' . $file;
    }
}
PluginsManager::initCron($event);

echo color::translateColorsCode('Cron job has completed &asuccessfully&r!&o');
