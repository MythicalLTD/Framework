<?php

namespace MythicalSystemsFramework\User;

use DateTime;
use MythicalSystemsFramework\Kernel\Encryption as enc;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystemsFramework\Managers\SnowFlakeManager;
use Gravatar\Gravatar;

class User
{

    /**
     * Create a user
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param string $ip
     * 
     * MAKE SURE YOU CHECK THE DOCS FOR THIS FUNCTION
     * @return string|null The user token
     */
    public static function create(string $username, string $password, string $email, string $first_name, string $last_name, string $ip): string|null
    {
        try {
            //Connect to the database
            $database = new \MythicalSystemsFramework\Database\MySQL();
            $mysqli = $database->connectMYSQLI();
            //New gravatar instance for avatars!
            $gravatar = new Gravatar([], true);
            //Check if the username exists
            $stmtUsername = $mysqli->prepare("SELECT COUNT(*) FROM framework_users WHERE username = ?");
            $stmtUsername->bind_param("s", $username);
            $stmtUsername->execute();
            $stmtUsername->bind_result($count);

            $stmtUsername->fetch();
            $stmtUsername->close();

            if ($count > 0) {
                return "ERROR_USERNAME_EXISTS";
            } else {
                //Check if the email exists
                $stmtEmail = $mysqli->prepare("SELECT COUNT(*) FROM framework_users WHERE email = ?");
                $stmtEmail->bind_param("s", $email);
                $stmtEmail->execute();
                $stmtEmail->bind_result($emailCount);

                $stmtEmail->fetch();
                $stmtEmail->close();
                if ($emailCount > 0) {
                    return "ERROR_EMAIL_EXISTS";
                } else {
                    //Insert the user into the database
                    $stmtInsert = $mysqli->prepare("INSERT INTO framework_users (username, first_name, last_name, email, password, avatar, uuid, token, first_ip, last_ip) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    //Hash the password
                    $password = enc::encrypt($password);
                    //Generate a unic user id! UUID
                    $uuid = SnowflakeManager::getUniqueUserID();
                    $account_token = "mythicalframework_".base64_encode( $uuid . '_' . DateTime::createFromFormat('U.u', microtime(true))->format("Y-m-d H:i:s.u") . rand(1000, 9999) . $ip . enc::encrypt($email));
                    $avatar_url = $gravatar->avatar($email);
                    $stmtInsert->bind_param("ssssssssss", $username, $first_name, $last_name, $email, $password, $avatar_url, $uuid, $account_token, $ip, $ip);
                    $stmtInsert->execute();
                    $stmtInsert->close();

                    return $account_token;
                }
            }
        } catch (\Exception $e) {
            return "ERROR_DATABASE_INSERT_FAILED";
        }
    }
}
