<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;

class Colors extends Command implements CommandBuilder
{
    public static string $description = 'A command to display the supported colors!';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        $colors = 'Colors: &0Black&r, &1Dark Blue&r, &2Dark Green&r, &3Dark Aqua&r, &4Dark Red&r, &5Dark Purple&r, &6Gold&r, &7Gray&r, &8Dark Gray&r, &9Blue&r, &aGreen&r, &bAqua&r, &cRed&r, &dLight Purple&r, &eYellow&r, &rWhite&r, &rReset&r, &lBold&r, &nUnderline&r, &mStrikethrough&r';
        echo self::translateColorsCode($colors);
        echo PHP_EOL;
        echo str_replace('&r', '', $colors);
    }
}
