<?php

use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Api\Api as api;
use MythicalSystemsFramework\Kernel\Debugger;
use MythicalSystemsFramework\Mail\MailService;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\User\UserHelper as user;
use MythicalSystemsFramework\Handlers\ActivityHandler;

api::init();
api::allowOnlyPOST();

// Hide all errors
Debugger::HideAllErrors();
try {
    if (isset($_POST['username'])) {
        api::makeSureValueIsNotNull($_POST['username'], ['message' => 'You are missing the post field for username!']);
    } else {
        api::BadRequest('You are missing the post field for username!', []);
    }

    if (isset($_POST['password'])) {
        api::makeSureValueIsNotNull($_POST['password'], ['message' => 'You are missing the post field for password!']);
    } else {
        api::BadRequest('You are missing the post field for password!', []);
    }

    if (isset($_POST['email'])) {
        api::makeSureValueIsNotNull($_POST['email'], ['message' => 'You are missing the post field for email!']);
    } else {
        api::BadRequest('You are missing the post field for email!', []);
    }

    if (isset($_POST['first_name'])) {
        api::makeSureValueIsNotNull($_POST['first_name'], ['message' => 'You are missing the post field for first_name!']);
    } else {
        api::BadRequest('You are missing the post field for first_name!', []);
    }

    if (isset($_POST['last_name'])) {
        api::makeSureValueIsNotNull($_POST['last_name'], ['message' => 'You are missing the post field for last_name!']);
    } else {
        api::BadRequest('You are missing the post field for last_name!', []);
    }

    $user = user::create($_POST['username'], $_POST['password'], $_POST['email'], $_POST['first_name'], $_POST['last_name'], MythicalSystems\CloudFlare\CloudFlare::getRealUserIP());

    if ($user == 'ERROR_USERNAME_EXISTS') {
        api::BadRequest('The username exists!', ['RESULT' => $user]);
    } elseif ($user == 'ERROR_EMAIL_EXISTS') {
        api::BadRequest('The email exists!', ['RESULT' => $user]);
    } elseif ($user == 'ERROR_DATABASE_INSERT_FAILED') {
        api::BadRequest('Failed to insert the user into the database!', ['RESULT' => $user]);
    } else {
        $user_id = user::getSpecificUserData($user, 'uuid', true);
        if (MailService::isEnabled() == true) {
            // TODO: Add a verify system
        } else {
            user::updateSpecificUserData($user, 'verified', 'true');

            ActivityHandler::addActivity($user_id, user::getSpecificUserData($user, 'username', true), 'User created an account!', MythicalSystems\CloudFlare\CloudFlare::getRealUserIP(), 'USER_CREATED');
            ActivityHandler::addActivity($user_id, user::getSpecificUserData($user, 'username', true), 'User verified his account!', MythicalSystems\CloudFlare\CloudFlare::getRealUserIP(), 'USER_VERIFIED');
        }
        api::OK('The user has been created!', ['TOKEN' => $user]);
    }
} catch (Exception $e) {
    api::InternalServerError('An error occurred!', ['ERROR' => $e->getMessage()]);
    Logger::log(LoggerTypes::CORE, LoggerLevels::CRITICAL, '(Api/User/register.php) An error occurred in the register.php file!');
}
