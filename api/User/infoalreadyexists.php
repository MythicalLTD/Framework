<?php

use MythicalSystems\Api\Api as api;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Kernel\Encryption;

api::init();
api::allowOnlyGET();
if (isset($_GET['info']) && !$_GET['info'] == '') {
    if (isset($_GET['value']) && !$_GET['value'] == '') {
        if (isset($_GET['isEncrypted']) && !$_GET['isEncrypted'] == '') {
            $isEncrypted = $_GET['isEncrypted'];
            $info = $_GET['info'];
            $value = $_GET['value'];

            if (isset($_GET['inVerted']) && !$_GET['inVerted'] == '') {
                if ($_GET['inVerted'] == 'true') {
                    $inVerted = true;
                } else {
                    $inVerted = false;
                }
            } else {
                $inVerted = false;
            }

            if ($isEncrypted == 'true') {
                $value = Encryption::encrypt($value);
                $user = UserHelper::doesInfoAboutExist($info, $value);
            } else {
                $user = UserHelper::doesInfoAboutExist($info, $value);
            }

            if ($user == 'INFO_EXISTS') {
                if ($inVerted == true) {
                    api::BadRequest('The info exists!', ['RESULT' => $user]);
                } else {
                    api::OK('The info exists!', ['RESULT' => $user]);
                }
            } elseif ($user == 'INFO_NOT_FOUND') {
                if ($inVerted == true) {
                    api::OK('The info does not exist!', ['RESULT' => $user]);
                } else {
                    api::BadRequest('The info does not exist!', ['RESULT' => $user]);
                }
            } elseif ($user == 'ERROR_DATABASE_SELECT_FAILED') {
                api::BadRequest('Failed to select the info from the database!', ['RESULT' => $user]);
            } else {
                api::BadRequest('An unknown error occurred!', ['RESULT' => $user]);
            }
        } else {
            api::BadRequest('You are missing the GET field for isEncrypted!', []);
        }
    } else {
        api::BadRequest('You are missing the GET field for value!', []);
    }
} else {
    api::BadRequest('You are missing the GET field for info!', []);
}
