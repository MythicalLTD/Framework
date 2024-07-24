<?php

namespace MythicalSystemsFramework\Kernel;

use MythicalSystems\Utils\EncryptionHandler as core_encryption;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Encryption
{
    /**
     * Encrypt the data
     *
     * @param string $data
     *
     * @return string
     */
    public static function encrypt(string $data): string
    {
        return core_encryption::encrypt($data, cfg::get("encryption", "key"));
    }

    /**
     * Decrypt the data
     *
     * @param string $data
     *
     * @return string
     */
    public static function decrypt(string $data): string
    {
        return core_encryption::decrypt($data, cfg::get("encryption", "key"));
    }
}
