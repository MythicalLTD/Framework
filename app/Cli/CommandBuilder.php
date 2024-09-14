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

namespace MythicalSystemsFramework\Cli;

interface CommandBuilder
{
    /**
     * Execute the command.
     *
     * @param bool $isFrameworkCommand Is it a framework command?
     * @param array $args the arguments passed to the command
     *
     * @return void it should return nothing as it is a void function
     */
    public static function execute(bool $isFrameworkCommand, array $args): void;
}
