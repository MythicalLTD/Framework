<?php

namespace MythicalSystemsFramework\Kernel;

use MythicalSystemsFramework\Managers\ConfigManager as cfg;
use MythicalSystems\Utils\EncryptionHandler as core_encryption;

class Encryption
{
    /**
     * Encrypt the data.
     */
    public static function encrypt(string $data): string
    {
        return core_encryption::encrypt($data, cfg::get('encryption', 'key'));
    }

    /**
     * Decrypt the data.
     */
    public static function decrypt(string $data): string
    {
        return core_encryption::decrypt($data, cfg::get('encryption', 'key'));
    }
}
