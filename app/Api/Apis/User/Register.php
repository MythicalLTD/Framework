<?php

namespace MythicalSystemsFramework\Api\Apis\User;

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Api\Apis\ApiBuilder;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\UserHelper as user;
use MythicalSystemsFramework\Handlers\ActivityHandler;

class Register extends Api implements ApiBuilder
{
    public string $route = '/user/register';

    public string $description = 'Register the user in the database!';

    public function handleRequest(): void
    {
        Api::allowOnlyPOST();

        // Hide all errors
        Debugger::HideAllErrors();
        try {
            if (isset($_POST['username'])) {
                Api::makeSureValueIsNotNull($_POST['username'], ['message' => 'You are missing the post field for username!']);
            } else {
                Api::BadRequest('You are missing the post field for username!', []);
            }

            if (isset($_POST['password'])) {
                Api::makeSureValueIsNotNull($_POST['password'], ['message' => 'You are missing the post field for password!']);
            } else {
                Api::BadRequest('You are missing the post field for password!', []);
            }

            if (isset($_POST['email'])) {
                Api::makeSureValueIsNotNull($_POST['email'], ['message' => 'You are missing the post field for email!']);
            } else {
                Api::BadRequest('You are missing the post field for email!', []);
            }

            if (isset($_POST['first_name'])) {
                Api::makeSureValueIsNotNull($_POST['first_name'], ['message' => 'You are missing the post field for first_name!']);
            } else {
                Api::BadRequest('You are missing the post field for first_name!', []);
            }

            if (isset($_POST['last_name'])) {
                Api::makeSureValueIsNotNull($_POST['last_name'], ['message' => 'You are missing the post field for last_name!']);
            } else {
                Api::BadRequest('You are missing the post field for last_name!', []);
            }

            $user = user::create($_POST['username'], $_POST['password'], $_POST['email'], $_POST['first_name'], $_POST['last_name'], \MythicalSystems\CloudFlare\CloudFlare::getRealUserIP());

            if ($user == 'ERROR_USERNAME_EXISTS') {
                Api::BadRequest('The username exists!', ['RESULT' => $user]);
            } elseif ($user == 'ERROR_EMAIL_EXISTS') {
                Api::BadRequest('The email exists!', ['RESULT' => $user]);
            } elseif ($user == 'ERROR_DATABASE_INSERT_FAILED') {
                Api::BadRequest('Failed to insert the user into the database!', ['RESULT' => $user]);
            } else {
                $user_id = user::getSpecificUserData($user, 'uuid', true);
                if (MailService::isEnabled() == true) {
                    // TODO: Add a verify system
                } else {
                    user::updateSpecificUserData($user, 'verified', 'true');

                    ActivityHandler::addActivity($user_id, user::getSpecificUserData($user, 'username', true), 'User created an account!', \MythicalSystems\CloudFlare\CloudFlare::getRealUserIP(), 'USER_CREATED');
                    ActivityHandler::addActivity($user_id, user::getSpecificUserData($user, 'username', true), 'User verified his account!', \MythicalSystems\CloudFlare\CloudFlare::getRealUserIP(), 'USER_VERIFIED');
                }
                Api::OK('The user has been created!', ['TOKEN' => $user]);
            }
        } catch (\Exception $e) {
            Api::InternalServerError('An error occurred!', ['ERROR' => $e->getMessage()]);
            Logger::log(LoggerTypes::CORE, LoggerLevels::CRITICAL, '(Api/User/register.php) An error occurred in the register.php file!');
        }
    }
}
