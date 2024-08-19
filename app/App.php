<?php

namespace MythicalSystemsFramework;

class App extends \MythicalSystems\Main
{
    /**
     * Call the garbage collector.
     */
    public static function callGarbageCollector(): void
    {
        gc_enable();
        gc_mem_caches();
        gc_collect_cycles();
        gc_disable();
    }
}
