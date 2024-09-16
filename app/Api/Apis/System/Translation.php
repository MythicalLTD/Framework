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

namespace MythicalSystemsFramework\Api\Apis\System;

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Api\Apis\ApiBuilder;

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

                $translation = $translation[$key] ?? $orElse;

                if ($translation == $orElse) {
                    $error = 'The translation does not exist!';
                } else {
                    $error = null;
                }

                if ($plain) {
                    exit($translation);
                }
                Api::OK('The translation exists!', ['RESULT' => $translation, 'result' => $translation, 'text' => $translation, 'message' => $translation, 'error' => $error]);

            } else {
                Api::BadRequest('You are missing the GET field for orElse!', []);
            }
        } else {
            Api::BadRequest('You are missing the GET field for key!', []);
        }
    }
}
