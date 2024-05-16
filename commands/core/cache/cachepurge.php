<?php 
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;

class cachepurgeCommand
{
    public function execute() {
        echo color::translateColorsCode("Please wait while we purge your &ccaches&r.&o");
        try {
            cache::purge();
            echo color::translateColorsCode("&rPurged caches!");
        } catch (\Exception $e) {
            echo color::translateColorsCode("&cFailed to purge caches: &r".$e->getMessage());
        } 
    }
}