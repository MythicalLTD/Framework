<?php
use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Database\MySQL;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class dbconfigureCommand
{
    public function execute()
    {
        $db = new MySQL();
        if ($db->tryConnection(cfg::get("database", "host"), cfg::get("database", "port"), cfg::get("database", "username"), cfg::get("database", "password"), cfg::get("database", "name")) == true) {
            echo color::translateColorsCode("&fConnection to the database was &asuccessful!&o");
            echo color::NewLine();
            echo color::translateColorsCode("&fDo you want to reconfigure the database? (&ey&f/&en&f): ");
            $confirm = readline();
            if (strtolower($confirm) === 'y') {
                $this->configure();
            } else {
                die(color::translateColorsCode("&fExiting...&o"));
            }
            return;
        } else {
            $this->configure();
        }
    }
    /**
     * Configures the database connection.
     * 
     * @return void
     */
    public function configure(): void
    {
        $defaultHost = "127.0.0.1";
        $defaultPort = "3306";
        $db = new MySQL();
        echo color::translateColorsCode("&fEnter the host of the database &8[&e$defaultHost&8]&f: ");
        $host = readline() ?: $defaultHost;
        echo color::translateColorsCode("&fEnter the port of the database &8[&e$defaultPort&8]&f: ");
        $port = readline() ?: $defaultPort;
        echo color::translateColorsCode("&fEnter the username: ");
        $username = readline();
        echo color::translateColorsCode("&fEnter the password: ");
        $password = readline();
        echo color::translateColorsCode("&fEnter the database name: ");
        $database = readline();

        // Perform validation
        if (empty($username) || empty($password) || empty($database)) {
            echo color::translateColorsCode("&cPlease provide all the required information.&o");
            return;
        }

        // Hide the password
        $hiddenPassword = str_repeat('*', strlen($password));

        // Use the provided information
        echo color::NewLine();
        echo color::translateColorsCode("&fHost: &e$host&o");
        echo color::translateColorsCode("&fPort: &e$port&o");
        echo color::translateColorsCode("&fUsername: &e$username&o");
        echo color::translateColorsCode("&fPassword: &e$hiddenPassword&o");
        echo color::translateColorsCode("&fDatabase: &e$database&o");

        if ($db->tryConnection($host, $port, $username, $password, $database) == true) {
            echo color::NewLine();
            echo color::translateColorsCode("&fConnection to the database was &asuccessful!&o");
            echo color::NewLine();
            echo color::translateColorsCode("&fSaving the configuration...&o");
            cfg::set("database", "host", $host);
            cfg::set("database", "port", $port);
            cfg::set("database", "username", $username);
            cfg::set("database", "password", $password);
            cfg::set("database", "name", $database);
            echo color::translateColorsCode("&fConfiguration saved &asuccessfully!&o");
        } else {
            echo color::translateColorsCode("&7Failed to connect to the database. &o&fPlease check the provided information.");
        }
    }

}