<?php

use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;

cache::process();
echo color::translateColorsCode('Cache processed by &acrontab&r!&o');
