<?php 
use MythicalSystemsFramework\Handlers\CacheHandler as cache;
use MythicalSystemsFramework\Cli\Colors as color;

cache::process();
echo color::translateColorsCode("Cache processed by &acrontab&r!&o");