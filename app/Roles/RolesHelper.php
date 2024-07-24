<?php

namespace MythicalSystemsFramework\Roles;

use Exception;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\User\UserHelper;

class RolesHelper extends RolesDataHandler
{
    /**
     * Get the role name from the role id
     *
     * @param string $role_id
     *
     * @return string|null
     */
    public static function getRoleName(int $role_id): ?string
    {
        return self::getSpecificRoleInfo($role_id, "name");
    }

}
