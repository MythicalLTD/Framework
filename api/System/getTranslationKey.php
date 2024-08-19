<?php

use MythicalSystems\Api\Api as api;
use MythicalSystemsFramework\Managers\LanguageManager;

api::init();
api::allowOnlyGET();

if (isset($_GET['key']) && !$_GET['key'] == '') {
    if (isset($_GET['orElse']) && !$_GET['orElse'] == '') {
        $key = $_GET['key'];
        $orElse = $_GET['orElse'];
        if (isset($_GET['plain'])) {
            $plain = true;
        } else {
            $plain = false;
        }

        $translation = LanguageManager::getLang();
        $translation = $translation[$key] ?? $orElse;

        if ($translation == $orElse) {
            $error = 'The translation does not exist!';
        } else {
            $error = null;
        }

        if ($plain) {
            exit($translation);
        } else {
            api::OK('The translation exists!', ['RESULT' => $translation, 'result' => $translation, 'text' => $translation, 'message' => $translation, 'error' => $error]);
        }
    } else {
        api::BadRequest('You are missing the GET field for orElse!', []);
    }
} else {
    api::BadRequest('You are missing the GET field for key!', []);
}
