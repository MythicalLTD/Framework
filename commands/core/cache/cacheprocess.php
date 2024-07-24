<?php

use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Handlers\CacheHandler as cache;

class cacheprocessCommand
{
    public function execute()
    {
        echo color::translateColorsCode("Please wait while we process your &ccaches&r.&o");
        try {
            cache::process();
            echo color::translateColorsCode("&rProcessed caches!");
        } catch (\Exception $e) {
            echo color::translateColorsCode("&cFailed to process caches: &r".$e->getMessage());
        }
    }
}
