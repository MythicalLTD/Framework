<?php

use MythicalSystemsFramework\Database\MySQLCache;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Cli\Colors as color;

$date_now = date("Y-m-d H:i:s");
MySQLCache::saveCache("framework_settings");
