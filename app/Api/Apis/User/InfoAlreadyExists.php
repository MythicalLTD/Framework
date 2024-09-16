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

namespace MythicalSystemsFramework\Api\Apis\User;

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Api\Apis\ApiBuilder;
use MythicalSystemsFramework\Encryption\XChaCha20;

class InfoAlreadyExists extends Api implements ApiBuilder
{
    public string $route = '/user/doesinfoexist';

    public string $description = 'Check if info about the user exists or not!';

    public function handleRequest(): void
    {
        Api::allowOnlyGET();
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
                        $value = XChaCha20::encrypt($value);
                        $user = 'NULL';
                    } else {
                        $user = 'NULL';
                    }

                    if ($user == 'INFO_EXISTS') {
                        if ($inVerted == true) {
                            Api::BadRequest('The info exists!', ['RESULT' => $user]);
                        } else {
                            Api::OK('The info exists!', ['RESULT' => $user]);
                        }
                    } elseif ($user == 'INFO_NOT_FOUND') {
                        if ($inVerted == true) {
                            Api::OK('The info does not exist!', ['RESULT' => $user]);
                        } else {
                            Api::BadRequest('The info does not exist!', ['RESULT' => $user]);
                        }
                    } elseif ($user == 'ERROR_DATABASE_SELECT_FAILED') {
                        Api::BadRequest('Failed to select the info from the database!', ['RESULT' => $user]);
                    } else {
                        Api::BadRequest('An unknown error occurred!', ['RESULT' => $user]);
                    }
                } else {
                    Api::BadRequest('You are missing the GET field for isEncrypted!', []);
                }
            } else {
                Api::BadRequest('You are missing the GET field for value!', []);
            }
        } else {
            Api::BadRequest('You are missing the GET field for info!', []);
        }
    }
}
