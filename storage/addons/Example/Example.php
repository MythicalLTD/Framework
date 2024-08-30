<?php

use MythicalSystemsFramework\Plugins\PluginBuilder;

class Example implements PluginBuilder
{
    public function Main()
    {
        // TODO: Implement the main function
    }

    public function Event(MythicalSystemsFramework\Plugins\PluginEvent $eventHandler)
    {
        $eventHandler->on('app.onRoutesLoaded', function ($router) {
            $router->add('/example', function () {
                exit('das');
            });
        });
    }
}
