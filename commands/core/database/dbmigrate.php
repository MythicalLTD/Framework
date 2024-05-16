<?php
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class dbmigrateCommand
{
    public function execute()
    {
        $db = new MySQL();
        if ($db->tryConnection(cfg::get("database", "host"), cfg::get("database", "port"), cfg::get("database", "username"), cfg::get("database", "password"), cfg::get("database", "name")) == true) {
            echo color::translateColorsCode("&fConnection to the database was &asuccessful!&o");
            echo color::NewLine();
            echo color::translateColorsCode("&fDo you want to migrate the database? (&ey&f/&en&f): ");
            $confirm = readline();
            if (strtolower($confirm) == 'y') {
                $this->migrate();
            } else {
                die(color::translateColorsCode("&fExiting...&o"));
            }
            return;
        } else {
            die(color::translateColorsCode("&cFailed to connect to the database!&o"));
        }
    }

    public function migrate()
    {
        try {
            $mysql = new MySQL();
            $db = $mysql->connectPDO();

            $db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                script VARCHAR(255) NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

            $sqlFiles = glob(__DIR__ . '/../../../migrate/database/*.sql');

            if (count($sqlFiles) > 0) {
                sort($sqlFiles); // Sort the SQL files in ascending order

                foreach ($sqlFiles as $sqlFile) {
                    $script = file_get_contents($sqlFile);

                    $fileName = basename($sqlFile); // Get only the file name

                    $stmt = $db->prepare("SELECT COUNT(*) FROM migrations WHERE script = ?");
                    $stmt->execute([$fileName]);
                    $count = $stmt->fetchColumn();

                    if ($count == 0) {
                        $db->exec($script);

                        $stmt = $db->prepare("INSERT INTO migrations (script) VALUES (?)");
                        $stmt->execute([$fileName]);

                        echo color::translateColorsCode("&fExecuted migration: &e" . $fileName . "&o");
                        echo color::NewLine();
                    } else {
                        echo color::translateColorsCode("&fSkipping migration: &e" . $fileName . " &f(&ealready executed&f)&o");
                        echo color::NewLine();
                    }
                }
            } else {
                echo color::translateColorsCode("&cNo migrations found!&o");
                echo color::NewLine();
            }
        } catch (PDOException $e) {
            echo color::translateColorsCode("&cFailed to migrate the database: " . $e->getMessage() . "&o");
            echo color::NewLine();
        }
    }
}