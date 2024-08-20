<?php

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
