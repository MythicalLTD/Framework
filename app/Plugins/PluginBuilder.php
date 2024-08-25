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
     * The route function of the plugin.
     *
     * This function can be used to add routes to the router.
     *
     * @param \Router\Router $router This is the router!
     * @param \Twig\Environment $renderer This is the renderer if you want to render pages!
     *
     * @return void
     */
    public function Route(\Router\Router $router, \Twig\Environment $renderer);

    /**
     * The event function of the plugin.
     *
     * This function can be used to get the event handler.
     *
     * @param PluginEvent $eventHandler This is the event handler!
     */
    public function Event(PluginEvent $eventHandler);
}
