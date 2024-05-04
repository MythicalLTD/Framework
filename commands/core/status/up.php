<?php 
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Cli\Colors as color;

class UpCommand
{
    public function execute()
    {   
        if (cfg::get('app', 'maintenance') == "false"  && cfg::get('app', 'maintenance') != "true") {
            echo color::translateColorsCode("&cApplication &fis already up!");
            return;
        }
        cfg::set('app', 'maintenance', "false");
        echo color::translateColorsCode("&cApplication &fis now up!");
    }
}