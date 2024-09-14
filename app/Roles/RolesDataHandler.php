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

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

class RolesDataHandler
{
    /**
     * Create a role.
     *
     * @param string $name The name of the role
     * @param int $weight The default is set to 1
     *
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     *
     * @return string|null The role id in a string
     */
    public static function create(string $name, int $weight = 1): ?string
    {
        global $event;
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            // Check if the role exists
            $stmtRole = $mysqli->prepare('SELECT COUNT(*) FROM framework_roles WHERE name = ?');
            $stmtRole->bind_param('s', $name);
            $stmtRole->execute();
            $stmtRole->bind_result($count);

            $stmtRole->fetch();
            $stmtRole->close();

            if ($count > 0) {
                return 'ERROR_ROLE_EXISTS';
            }
            // Insert the role into the database
            $stmtInsert = $mysqli->prepare('INSERT INTO framework_roles (name, weight) VALUES (?, ?)');

            $stmtInsert->bind_param('si', $name, $weight);
            $stmtInsert->execute();
            $stmtInsert->close();
            $event->emit('roles.Create', [$name, $weight]);

            return $mysqli->insert_id;

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesDataHandler.php) Failed to create role: ' . $e->getMessage());

            return 'ERROR_DATABASE_INSERT_FAILED';
        }
    }

    /**
     * Delete a role.
     *
     * @param int $id The role id
     *
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     */
    public static function delete(int $id): ?string
    {
        global $event;
        try {
            if (self::roleExists($id) == 'ROLE_MISSING') {
                return 'ROLE_MISSING';
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            $event->emit('roles.Delete', [$id]);
            // Delete the role
            $stmtRole = $mysqli->prepare('DELETE FROM framework_roles WHERE id = ?');
            $stmtRole->bind_param('i', $id);
            $stmtRole->execute();
            $stmtRole->close();
            if ($mysqli->affected_rows > 0) {
                return 'ROLE_DELETED';
            }

            return 'ROLE_DELETE_FAILED';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesDataHandler.php) Failed to delete role: ' . $e->getMessage());

            return 'ERROR_DATABASE_DELETE_FAILED';
        }
    }

    /**
     * Update a role.
     *
     * @param int $id The role id
     * @param string $name The name of the role
     * @param int $weight The default is set to 1
     *
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     */
    public static function update(int $id, string $name, int $weight = 1): ?string
    {
        global $event;
        try {
            if (self::roleExists($id) == 'ROLE_MISSING') {
                return 'ROLE_MISSING';
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            $event->emit('roles.Update', [$id, $name, $weight]);
            // Update the role
            $stmtRole = $mysqli->prepare('UPDATE framework_roles SET name = ?, weight = ? WHERE id = ?');
            $stmtRole->bind_param('sii', $name, $weight, $id);
            $stmtRole->execute();
            $stmtRole->close();
            if ($mysqli->affected_rows > 0) {
                return 'ROLE_UPDATED';
            }

            return 'ROLE_UPDATE_FAILED';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesDataHandler.php) Failed to update role: ' . $e->getMessage());

            return 'ERROR_DATABASE_UPDATE_FAILED';
        }
    }

    /**
     * Get a role.
     *
     * @param int $id The role id
     * @param string $data The data you are looking for
     *
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     */
    public static function getSpecificRoleInfo(int $id, string $data): ?string
    {
        try {
            if (self::roleExists($id) == 'ROLE_MISSING') {
                return 'ROLE_MISSING';
            }
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Get the role info
            $stmtRole = $mysqli->prepare("SELECT $data FROM framework_roles WHERE id = ?");
            $stmtRole->bind_param('i', $id);
            $stmtRole->execute();
            $stmtRole->bind_result($result);
            $stmtRole->fetch();
            $stmtRole->close();

            return $result;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesDataHandler.php) Failed to get role info: ' . $e->getMessage());

            return 'ERROR_DATABASE_SELECT_FAILED';
        }
    }

    /**
     * Get all roles with name, weight, and id.
     *
     * @return array|null An array of roles with name, weight, and id
     */
    public static function getAllRoles(): ?array
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Get all roles
            $stmtRoles = $mysqli->prepare('SELECT id, name, weight FROM framework_roles');
            $stmtRoles->execute();
            $stmtRoles->bind_result($id, $name, $weight);

            $roles = [];
            while ($stmtRoles->fetch()) {
                $roles[] = [
                    'id' => $id,
                    'name' => $name,
                    'weight' => $weight,
                ];
            }

            $stmtRoles->close();

            return $roles;
        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesDataHandler.php) Failed to get all roles: ' . $e->getMessage());

            return null;
        }
    }

    /**
     * This function just looks if the role exists.
     *
     * @param int $id The name of the role
     */
    public static function roleExists(int $id): ?string
    {
        try {
            // Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();

            // Check if the role exists
            $stmtRole = $mysqli->prepare('SELECT COUNT(*) FROM framework_roles WHERE id = ?');
            $stmtRole->bind_param('i', $id);
            $stmtRole->execute();
            $stmtRole->bind_result($count);
            $stmtRole->fetch();
            $stmtRole->close();

            if ($count > 0) {
                return 'ROLE_EXISTS';
            }

            return 'ROLE_MISSING';

        } catch (\Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::DATABASE, '(App/Roles/RolesDataHandler.php) Failed to check if role exists: ' . $e->getMessage());

            return 'ERROR_DATABASE_INSERT_FAILED';
        }
    }
}
