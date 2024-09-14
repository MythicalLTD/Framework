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

namespace MythicalSystemsFramework\Plugins\Interfaces;

interface Stability
{
    public const STABILITY_ALPHA = 'alpha';
    public const STABILITY_BETA = 'beta';
    public const STABILITY_DEV = 'dev';
    public const STABILITY_RC = 'rc';
    public const STABILITY_STABLE = 'stable';
}
