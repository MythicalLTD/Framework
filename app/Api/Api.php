<?php

namespace MythicalSystemsFramework\Api;

class Api extends \MythicalSystems\Api\Api
{
    public static function makeSureValueIsNotNull(string $info, ?array $array): void
    {
        if (!$info == '') {
            return;
        } else {
            self::BadRequest("You are missing the field for $info!", $array);
        }
    }

    /**
     * Register all api endpoints.
     */
    public static function registerApiRoutes(\Router\Router $router): void
    {
        $admin_folder = __DIR__ . '/Apis/Admin';
        $user_folder = __DIR__ . '/Apis/User';
        $system_folder = __DIR__ . '/Apis/System';

        $admin_files = scandir($admin_folder);
        $user_files = scandir($user_folder);
        $system_files = scandir($system_folder);

        foreach ($system_files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $class = 'MythicalSystemsFramework\Api\Apis\System\\' . str_replace('.php', '', $file);
            $class = new $class();
            $router->add('/api' . $class->route, function () use ($class) {
                Api::init();
                $class->handleRequest();
            });
        }

        foreach ($admin_files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $class = 'MythicalSystemsFramework\Api\Apis\Admin\\' . str_replace('.php', '', $file);
            $class = new $class();
            $router->add('/api' . $class->route, function () use ($class) {
                Api::init();
                $class->handleRequest();
            });
        }

        foreach ($user_files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $class = 'MythicalSystemsFramework\Api\Apis\User\\' . str_replace('.php', '', $file);
            $class = new $class();
            $router->add('/api' . $class->route, function () use ($class) {
                Api::init();
                $class->handleRequest();
            });
        }

        $router->add('/api/(.*)', function () {
            self::init();
            self::NotFound('The api route does not exist!', null);
        });
    }

    /**
     * Extracts the dynamic argument based on the route structure.
     *
     * @param string $route The route it should include (.*) if you are looking for a dynamic argument
     * @param int $aindex This is more like a game :) You need to guess the index of the dynamic argument
     *
     * @return string The dynamic argument
     *
     * For people who think they can optimize this function:
     *
     * I have tried my best to optimize this function as much as possible.
     * If you think you can optimize it further, please do so and create a pull request.
     * But if not make sure to increase the following line with the hours you wasted over here:
     *
     * @time 1 hour
     *
     * For the people who think they know what this function does:
     * You don't trust me on this one, do you? Well, I can assure you that this function is the best function you will ever see in your life.
     */
    public static function getRouteArg(string $route, int $aindex = 1): string
    {
        // Break down the route and the current URI into their segments
        $routeParts = explode('/', trim($route, '/'));
        $uriParts = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));

        // Find the part of the URI that matches the "(.*)" in the route
        foreach ($routeParts as $index => $part) {
            if ($part === '(.*)') {
                // +1 cuz we have /api in front and the code does not know that we have that
                // so we need to adjust the index by 1 because of that so yeah do not increase the index by 1 or remove it
                // Doing that is gay! (no offense) Just kidding, but seriously do not do that.
                // I mean we can technically add /api before the $route up there in the code but that would be a waste of time
                // and we do not want to waste time, do we? Nahh we like wasting time on comments like those :)
                // So yeah, do not remove the +1 or increase the index by 1.
                $adjustedIndex = $index + $aindex;

                return $uriParts[$adjustedIndex] ?? '';
            }
        }

        return '';
    }
}
