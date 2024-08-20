<?php

namespace MythicalSystemsFramework\Api\Apis\System;

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Api\Apis\ApiBuilder;
use MythicalSystemsFramework\Managers\LanguageManager;

class Translation extends Api implements ApiBuilder
{
    public string $route = '/system/translation';

    public string $description = 'This route will return the translation for the given key';

    public function handleRequest(): void
    {
        self::allowOnlyGET();
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
                    Api::OK('The translation exists!', ['RESULT' => $translation, 'result' => $translation, 'text' => $translation, 'message' => $translation, 'error' => $error]);
                }
            } else {
                Api::BadRequest('You are missing the GET field for orElse!', []);
            }
        } else {
            Api::BadRequest('You are missing the GET field for key!', []);
        }
    }
}
