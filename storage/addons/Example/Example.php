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
        $eventHandler->on('app.onAppLoad', function ($router,$renderer) {
            $router->add('/example', function () {
                exit('das');
            });
        });

        $eventHandler->on('backup.onBackupTake', function ($path) {

        });
    }
}
