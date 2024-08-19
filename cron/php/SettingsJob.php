<?php

use MythicalSystemsFramework\Database\MySQLCache;

$date_now = date('Y-m-d H:i:s');
MySQLCache::saveCache('framework_settings');
