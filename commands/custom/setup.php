<?php
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class setupCommand
{
    public function execute()
    {
        $db = new MySQL();
        if ($db->tryConnection(cfg::get("database", "host"), cfg::get("database", "port"), cfg::get("database", "username"), cfg::get("database", "password"), cfg::get("database", "name")) == true) {
            echo color::translateColorsCode("&fConnection to the database was &asuccessful!&o");
            echo color::NewLine();
            if (cfg::get("encryption","key") == "") {
                die(color::translateColorsCode("&cEncryption key is not set!&o&fHave you set it up already?&o"));
            }
        } else {
            die(color::translateColorsCode("&cFailed to connect to the database!&o&fHave you set it up already?&o"));
        }
    }
}