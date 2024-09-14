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

namespace MythicalSystemsFramework\Kernel;

interface LoggerTypes
{
    // Log types
    public const CORE = 'CORE';
    public const DATABASE = 'DATABASE';
    public const PLUGIN = 'PLUGIN';
    public const LOG = 'LOG';
    public const LANGUAGE = 'LANGUAGE';
    public const BACKUP = 'BACKUP';
    // Other
    public const OTHER = 'OTHER';
}
