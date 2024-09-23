<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

namespace MythicalSystemsFramework\Roles;

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class RolesPermissionDataHandler
{
    /**
     * Create a role permission.
     *
     * @param int $roleId The role id
     * @param string $permission The permission
     *
     * @return string|null The role permission id in a string
     */
    public static function create(int $roleId, string $permission): ?string
    {
        global $event;

        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Insert the role permission into the database
            $stmtInsert = $mysqli->prepare('INSERT INTO framework_roles_permissions (role_id, permission) VALUES (?, ?)');
            $stmtInsert->bind_param('is', $roleId, $permission);
            $stmtInsert->execute();
            $stmtInsert->close();
            $event->emit('roles_permissions.Create', [$roleId, $permission]);

            return $mysqli->insert_id;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to create role permission: ' . $e->getMessage());

            return 'ERROR_DATABASE_INSERT_FAILED';
        }
    }

    /**
     * Delete a role permission.
     *
     * @param int $id The role permission id
     */
    public static function delete(int $id): ?string
    {
        global $event;

        try {
            if (self::rolePermissionExists($id) == 'ROLE_PERMISSION_MISSING') {
                return 'ROLE_PERMISSION_MISSING';
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            $event->emit('roles_permissions.Delete', [$id]);
            // Delete the role permission
            $stmtRole = $mysqli->prepare('DELETE FROM framework_roles_permissions WHERE id = ?');
            $stmtRole->bind_param('i', $id);
            $stmtRole->execute();
            $stmtRole->close();

            if ($mysqli->affected_rows > 0) {
                return 'ROLE_PERMISSION_DELETED';
            }

            return 'ROLE_PERMISSION_DELETE_FAILED';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to delete role permission: ' . $e->getMessage());

            return 'ERROR_DATABASE_DELETE_FAILED';
        }
    }

    /**
     * Update a role permission.
     *
     * @param int $id The role permission id
     * @param string $permission The permission
     */
    public static function update(int $id, string $permission): ?string
    {
        global $event;

        try {
            if (self::rolePermissionExists($id) == 'ROLE_PERMISSION_MISSING') {
                return 'ROLE_PERMISSION_MISSING';
            }

            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            $event->emit('roles_permissions.Update', [$id, $permission]);

            // Update the role permission
            $stmtRole = $mysqli->prepare('UPDATE framework_roles_permissions SET permission = ? WHERE id = ?');
            $stmtRole->bind_param('si', $permission, $id);
            $stmtRole->execute();
            $stmtRole->close();

            if ($mysqli->affected_rows > 0) {
                return 'ROLE_PERMISSION_UPDATED';
            }

            return 'ROLE_PERMISSION_UPDATE_FAILED';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to update role permission: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * Get all permissions for a role.
     *
     * @param int $roleId The role id
     *
     * @return array|null An array of permissions or null if role does not exist
     */
    public static function getAllPermissionsForRole(int $roleId): ?array
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Check if the role exists
            if (self::rolePermissionExists($roleId) == 'ROLE_PERMISSION_MISSING') {
                return null;
            }

            // Get all permissions for the role
            $stmtRole = $mysqli->prepare('SELECT permission FROM framework_roles_permissions WHERE role_id = ?');
            $stmtRole->bind_param('i', $roleId);
            $stmtRole->execute();
            $stmtRole->bind_result($permission);

            $permissions = [];
            while ($stmtRole->fetch()) {
                $permissions[] = $permission;
            }

            $stmtRole->close();

            return $permissions;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to get permissions for role: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * Get a role permission.
     *
     * @param int $id The role permission id
     * @param string $data The data you are looking for
     */
    public static function getSpecificRolePermissionInfo(int $id, string $data): ?string
    {
        try {
            if (self::rolePermissionExists($id) == 'ROLE_PERMISSION_MISSING') {
                return 'ROLE_PERMISSION_MISSING';
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Get the role permission info
            $stmtRole = $mysqli->prepare("SELECT $data FROM framework_roles_permissions WHERE id = ?");
            $stmtRole->bind_param('i', $id);
            $stmtRole->execute();
            $stmtRole->bind_result($result);
            $stmtRole->fetch();
            $stmtRole->close();

            return $result;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to get role permission info: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * This function checks if the role permission exists.
     *
     * @param int $id The role permission id
     */
    public static function rolePermissionExists(int $id): ?string
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Check if the role permission exists
            $stmtRole = $mysqli->prepare('SELECT COUNT(*) FROM framework_roles_permissions WHERE id = ?');
            $stmtRole->bind_param('i', $id);
            $stmtRole->execute();
            $stmtRole->bind_result($count);
            $stmtRole->fetch();
            $stmtRole->close();

            if ($count > 0) {
                return 'ROLE_PERMISSION_EXISTS';
            }

            return 'ROLE_PERMISSION_MISSING';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to check if role permission exists: ' . $e->getMessage());

            return 'ERROR_DATABASE_INSERT_FAILED';
        }
    }

    /**
     * Check if a role has a specific permission.
     *
     * @param int $roleId The role id
     * @param string $permission The permission
     * 
     * @return bool
     */
    public static function doesRoleHavePermission(int $roleId, string $permission): bool
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Check if the role has the permission
            $permissionWildcard = str_replace('*', '%', $permission);
            // Check if the role has the wildcard or specific permission
            $stmtRole = $mysqli->prepare('SELECT COUNT(*) FROM framework_roles_permissions WHERE role_id = ? AND (permission = "*" OR permission LIKE ?) AND status = "true"');
            $stmtRole->bind_param('is', $roleId, $permissionWildcard);
            $stmtRole->execute();
            $stmtRole->bind_result($count);
            $stmtRole->fetch();
            $stmtRole->close();

            if ($count > 0) {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesPermissionDataHandler.php) Failed to check role permission: ' . $e->getMessage());
            return false;
        }
    }
}
