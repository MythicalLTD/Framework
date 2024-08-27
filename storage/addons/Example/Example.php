<?php

use MythicalSystemsFramework\Plugins\PluginBuilder;

class Example implements PluginBuilder
{
    public function Main()
    {
        // TODO: Implement the main function
    }

    public function Route(Router\Router $router, Twig\Environment $renderer)
    {
        $router->add('/router/example', function () {
            exit('Routes worked!');
        });
    }

    public function Event(MythicalSystemsFramework\Plugins\PluginEvent $eventHandler)
    {
        $eventHandler->on('app.onRoutesLoaded', function ($router) {
            $router->add('/example', function () {
                exit('Events worked!');
            });
        });

        $eventHandler->on('user.onLoad', function () {
            exit('Events worked!');
        });
    }
}
