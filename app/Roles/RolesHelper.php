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

namespace MythicalSystemsFramework\Roles;

class RolesHelper extends RolesDataHandler
{
    /**
     * Get the role name from the role id.
     *
     * @param string $role_id
     */
    public static function getRoleName(int $role_id): ?string
    {
        return self::getSpecificRoleInfo($role_id, 'name');
    }
}
