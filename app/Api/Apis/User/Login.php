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

namespace MythicalSystemsFramework\Api\Apis\User;

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Api\Apis\ApiBuilder;

class Login extends Api implements ApiBuilder
{
    public string $route = '/user/login';

    public string $description = 'Get the user token with the password and email!';

    public function handleRequest(): void
    {
        Api::allowOnlyGET();
        if (isset($_GET['email']) && !$_GET['email'] == '') {
            if (isset($_GET['password']) && !$_GET['password'] == '') {
                $email = $_GET['email'];
                $password = $_GET['password'];
            } else {
                Api::BadRequest('You are missing the GET field for password!', []);
            }
        } else {
            Api::BadRequest('You are missing the GET field for email!', []);
        }
    }
}
