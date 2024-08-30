<?php

namespace MythicalSystemsFramework\Plugins;

interface PluginBuilder
{
    /**
     * The main function of the plugin.
     *
     * This function runs when the plugin gets loaded.
     *
     * @return void
     */
    public function Main();

    /**
     * The event function of the plugin.
     *
     * This function can be used to get the event handler.
     *
     * @param PluginEvent $eventHandler This is the event handler!
     */
    public function Event(PluginEvent $eventHandler);
}
