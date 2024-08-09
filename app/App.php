<?php

namespace MythicalSystemsFramework;

class App extends \MythicalSystems\Main
{
    /**
     * Convert a string to a bool.
     */
    public static function convertStringToBool(string $value): bool
    {
        if ($value == 'true') {
            return true;
        } else {
            return false;
        }
    }
}
