<?php

namespace MythicalSystemsFramework\Web\Installer;

use MythicalSystemsFramework\App;

class Installer
{
    /**
     * Is the app installed?
     */
    public static function Installed(\Router\Router $router): void
    {
        App::checkForRequirements();
        /*
         * Check if the app is installed
         */
        if (file_exists(__DIR__ . '/../../../storage/FIRST_INSTALL')) {
            $router->add('/', function () {
                include __DIR__ . '/index.php';
            });

            $router->add('/mysql', function () {
                include __DIR__ . '/ mysql.php';
            });

            $router->add('/install', function () {
                include __DIR__ . '/ install.php';
            });

            $router->add('/(.*)', function () {
                header('location: /');
            });
            $router->route();
            exit;
        } else {
            return;
        }
    }

    public static function showError(string $description)
    {
        exit("
        <html>
        <head>
        <title>Mythical Systems Framework</title>
        <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            color: #333;
        }
            .container {
            width: 50%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
            </style>
            </head>
            <body>
            <div class='container'>
            <h1>Mythical Systems Framework</h1>
            <p>" . $description . "</p>
            <p>Click <a href='/'>here</a> to reload.</p>
            </div>
            </body>
            </html>
            ");
    }
}
