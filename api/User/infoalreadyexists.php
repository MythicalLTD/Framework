<?php

use MythicalSystems\Api\Api as api;
use MythicalSystemsFramework\User\UserHelper;

api::init();
api::allowOnlyGET();
if (isset($_GET['info']) && !$_GET['info'] == '') {
    if (isset($_GET['value']) && !$_GET['value'] == '') {
        $info = $_GET['info'];
        $value = $_GET['value'];
        $user = UserHelper::doesInfoAboutExist($info, $value);
        if ($user == 'INFO_EXISTS') {
            api::OK('The info exists!', ['RESULT' => $user]);
        } elseif ($user == 'INFO_NOT_FOUND') {
            api::BadRequest('The info does not exist!', ['RESULT' => $user]);
        }
    } else {
        api::BadRequest('You are missing the post field for value!', []);
    }
} else {
    api::BadRequest('You are missing the post field for info!', []);
}
