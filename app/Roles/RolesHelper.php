<?php

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
