<?php 
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class newkeyCommand
{
    public function execute()
    {
        if (cfg::get("encryption","key") == "") {

        }
    }
}