<?php

namespace MythicalSystemsFramework\Api;

class Api extends \MythicalSystems\Api\Api
{
    public static function makeSureValueIsNotNull(string $info, ?array $array): void
    {
        if (!$info == '') {
            return;
        } else {
            self::BadRequest("You are missing the field for $info!", $array);
        }
    }
}
