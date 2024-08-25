<?php

use MythicalSystemsFramework\Plugins\PluginBuilder;

class Test implements PluginBuilder
{
    public function Main()
    {
        // TODO: Implement the main function
    }

    public function Route(Router\Router $router, Twig\Environment $renderer)
    {
    }

    public function Event(MythicalSystemsFramework\Plugins\PluginEvent $eventHandler)
    {
    }
}
