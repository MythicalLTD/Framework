<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

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
            $router->add('/', function (): void {
                include __DIR__ . '/index.php';
            });

            $router->add('/mysql', function (): void {
                include __DIR__ . '/mysql.php';
            });

            $router->add('/install', function (): void {
                include __DIR__ . '/install.php';
            });

            $router->add('/(.*)', function (): void {
                header('location: /');
            });
            $router->route();
            exit;
        }

    }

    public static function showError(string $description): void
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
