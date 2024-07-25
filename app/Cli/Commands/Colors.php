<?php

namespace MythicalSystemsFramework\Cli\Commands;

class Colors extends Command
{
    public static string $description = 'A command to display the supported colors!';

    public static function execute(bool $isFrameworkCommand = false): void
    {
        $colors = 'Colors: &0Black&r, &1Dark Blue&r, &2Dark Green&r, &3Dark Aqua&r, &4Dark Red&r, &5Dark Purple&r, &6Gold&r, &7Gray&r, &8Dark Gray&r, &9Blue&r, &aGreen&r, &bAqua&r, &cRed&r, &dLight Purple&r, &eYellow&r, &rWhite&r, &rReset&r, &lBold&r, &nUnderline&r, &mStrikethrough&r';
        echo self::translateColorsCode($colors);
        echo PHP_EOL;
        echo str_replace('&r', '', $colors);
    }
}
