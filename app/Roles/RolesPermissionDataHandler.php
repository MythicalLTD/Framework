<?php

namespace MythicalSystemsFramework\Roles;

use Exception;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Kernel\LoggerTypes;


class RolesPermissionDataHandler
{
    /**
     * Create a role permission
     * 
     * @param int $roleId The role id
     * @param string $permission The permission
     * 
     * @return string|null The role permission id in a string
     */
    public static function create(int $roleId, string $permission): string|null
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Insert the role permission into the database
            $stmtInsert = $mysqli->prepare("INSERT INTO framework_roles_permissions (role_id, permission) VALUES (?, ?)");
            $stmtInsert->bind_param("is", $roleId, $permission);
            $stmtInsert->execute();
            $stmtInsert->close();

            return $mysqli->insert_id;
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to create role permission: " . $e->getMessage());
            return "ERROR_DATABASE_INSERT_FAILED";
        }
    }

    /**
     * Delete a role permission
     * 
     * @param int $id The role permission id
     * 
     * @return string|null
     */
    public static function delete(int $id): string|null
    {
        try {
            if (self::rolePermissionExists($id) == "ROLE_PERMISSION_MISSING") {
                return "ROLE_PERMISSION_MISSING";
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Delete the role permission
            $stmtRole = $mysqli->prepare("DELETE FROM framework_roles_permissions WHERE id = ?");
            $stmtRole->bind_param("i", $id);
            $stmtRole->execute();
            $stmtRole->close();

            if ($mysqli->affected_rows > 0) {
                return "ROLE_PERMISSION_DELETED";
            } else {
                return "ROLE_PERMISSION_DELETE_FAILED";
            }
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to delete role permission: " . $e->getMessage());
            return "ERROR_DATABASE_DELETE_FAILED";
        }
    }

    /**
     * Update a role permission
     * 
     * @param int $id The role permission id
     * @param string $permission The permission
     * 
     * @return string|null
     */
    public static function update(int $id, string $permission): string|null
    {
        try {
            if (self::rolePermissionExists($id) == "ROLE_PERMISSION_MISSING") {
                return "ROLE_PERMISSION_MISSING";
            }

            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Update the role permission
            $stmtRole = $mysqli->prepare("UPDATE framework_roles_permissions SET permission = ? WHERE id = ?");
            $stmtRole->bind_param("si", $permission, $id);
            $stmtRole->execute();
            $stmtRole->close();

            if ($mysqli->affected_rows > 0) {
                return "ROLE_PERMISSION_UPDATED";
            } else {
                return "ROLE_PERMISSION_UPDATE_FAILED";
            }
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to update role permission: " . $e->getMessage());
            return "ERROR_DATABASE_UPDATE_FAILED";
        }
    }

    /**
     * Get all permissions for a role
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
            if (self::rolePermissionExists($roleId) == "ROLE_PERMISSION_MISSING") {
                return null;
            }

            // Get all permissions for the role
            $stmtRole = $mysqli->prepare("SELECT permission FROM framework_roles_permissions WHERE role_id = ?");
            $stmtRole->bind_param("i", $roleId);
            $stmtRole->execute();
            $stmtRole->bind_result($permission);

            $permissions = [];
            while ($stmtRole->fetch()) {
                $permissions[] = $permission;
            }

            $stmtRole->close();

            return $permissions;
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to get permissions for role: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a role permission
     * 
     * @param int $id The role permission id
     * @param string $data The data you are looking for
     * 
     * @return string|null
     */
    public static function getSpecificRolePermissionInfo(int $id, string $data): string|null
    {
        try {
            if (self::rolePermissionExists($id) == "ROLE_PERMISSION_MISSING") {
                return "ROLE_PERMISSION_MISSING";
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Get the role permission info
            $stmtRole = $mysqli->prepare("SELECT $data FROM framework_roles_permissions WHERE id = ?");
            $stmtRole->bind_param("i", $id);
            $stmtRole->execute();
            $stmtRole->bind_result($result);
            $stmtRole->fetch();
            $stmtRole->close();

            return $result;
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to get role permission info: " . $e->getMessage());
            return "ERROR_DATABASE_SELECT_FAILED";
        }
    }

    /**
     * This function checks if the role permission exists
     * 
     * @param int $id The role permission id
     * 
     * @return string|null
     */
    public static function rolePermissionExists(int $id): string|null
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Check if the role permission exists
            $stmtRole = $mysqli->prepare("SELECT COUNT(*) FROM framework_roles_permissions WHERE id = ?");
            $stmtRole->bind_param("i", $id);
            $stmtRole->execute();
            $stmtRole->bind_result($count);
            $stmtRole->fetch();
            $stmtRole->close();

            if ($count > 0) {
                return "ROLE_PERMISSION_EXISTS";
            } else {
                return "ROLE_PERMISSION_MISSING";
            }
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to check if role permission exists: " . $e->getMessage());
            return "ERROR_DATABASE_INSERT_FAILED";
        }
    }

    /**
     * Check if a role has a specific permission
     * 
     * @param int $roleId The role id
     * @param string $permission The permission
     * 
     * @return string|null 
     */
    public static function doesRoleHavePermission(int $roleId, string $permission): string|null
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Check if the role has the permission
            $stmtRole = $mysqli->prepare("SELECT COUNT(*) FROM framework_roles_permissions WHERE role_id = ? AND permission = ?");
            $stmtRole->bind_param("is", $roleId, $permission);
            $stmtRole->execute();
            $stmtRole->bind_result($count);
            $stmtRole->fetch();
            $stmtRole->close();

            if ($count > 0) {
                return "ROLE_HAS_PERMISSION";
            } else {
                return "ROLE_DOES_NOT_HAVE_PERMISSION";
            }
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, "(App/Roles/RolesPermissionDataHandler.php) Failed to check role permission: " . $e->getMessage());
            return "ERROR_DATABASE_SELECT_FAILED";
        }
    }
}
