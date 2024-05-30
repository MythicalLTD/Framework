<?php 
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Cli\Colors as color;

class DownCommand
{
    public function execute()
    {
        if (cfg::get('app', 'maintenance') == "true" && cfg::get('app', 'maintenance') != "false") {
            echo color::translateColorsCode("&cApplication &7is already in maintainance mode!");
            return;
        }
        cfg::set('app', 'maintenance', "true");
        echo color::translateColorsCode("&cApplication &7set to maintainance mode!");
    }
}