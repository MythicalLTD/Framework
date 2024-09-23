<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

namespace MythicalSystemsFramework\Plugins;

interface PluginBuilder
{
    /**
     * The main function of the plugin.
     *
     * This function runs when the plugin gets loaded.
     */
    public function Main(): void;

    /**
     * The event function of the plugin.
     *
     * This function can be used to get the event handler.
     *
     * @param PluginEvent $eventHandler This is the event handler!
     */
    public function Event(PluginEvent $eventHandler);
    /*
    * The install function of the plugin.
    */
    public function onInstall(): void;

    /**
     * The uninstall function of the plugin.
     */
    public function onUninstall(): void;

    /**
     * The update function of the plugin.
     */
    public function onUpdate(): void;
}
