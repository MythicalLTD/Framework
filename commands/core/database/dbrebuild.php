<?php 
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class dbrebuildCommand
{
    public function execute()
    {
        $db = new MySQL();
        if ($db->tryConnection(cfg::get("database", "host"), cfg::get("database", "port"), cfg::get("database", "username"), cfg::get("database", "password"), cfg::get("database", "name")) == true) {
            echo color::translateColorsCode("&fConnection to the database was &asuccessful!&o");
            echo color::NewLine();
            echo color::translateColorsCode("&4&lWARNING: &fThis option will wipe the database. &o");
            echo color::translateColorsCode("&4&lWARNING: &fOnly use this function if you know what you are doing &o");
            echo color::translateColorsCode("&4&lWARNING: &fOnce you wipe the database there is no going back! &o");
            echo color::translateColorsCode("&4&lWARNING: &fPlease be careful and don't play around with commands!  &o");
            echo color::translateColorsCode("&4&lWARNING: &fThere is no other message then this so keep in mind! &o");
            echo color::translateColorsCode("&4&lWARNING: &fDo you really want to wipe the database? (&ey&f/&en&f): ");
            
            $confirm = readline();
            if (strtolower($confirm) == 'y') {
                $this->wipe();
            } else {
                die(color::translateColorsCode("&fExiting...&o"));
            }
            return;
        } else {
            die(color::translateColorsCode("&cFailed to connect to the database!&o"));
        }
    }

    public function wipe() {
        try {
            $mysql = new MySQL();
            $db = $mysql->connect();

            $db->exec("SET FOREIGN_KEY_CHECKS = 0");
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            foreach ($tables as $table) {
                $db->exec("DROP TABLE IF EXISTS $table");
            }
            $db->exec("SET FOREIGN_KEY_CHECKS = 1");
            echo color::NewLine();
            echo color::NewLine();
            echo color::NewLine();
            echo color::NewLine();
            echo color::NewLine();
            echo color::translateColorsCode("&fDatabase wiped!!&o");
            echo color::translateColorsCode("&fPlease run the migration command to rebuild the database!&o");
        } catch (PDOException $e) {
            echo color::translateColorsCode("&fFailed to drop tables: &c" . $e->getMessage() . "&o");
            echo color::NewLine();
        }
    }
}