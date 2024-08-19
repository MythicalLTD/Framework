<?php

namespace MythicalSystemsFramework\Encryption;

use MythicalSystems\Utils\XChaCha20 as XChaCha20Util;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class XChaCha20 implements Types
{
    public static function decrypt(string $text): string
    {
        return XChaCha20Util::decrypt($text, cfg::get('encryption', 'key'), true);
    }

    public static function encrypt(string $text): string
    {
        return XChaCha20Util::encrypt($text, cfg::get('encryption', 'key'), true);
    }

    public static function generateKey(): string
    {
        $key = XChaCha20Util::generateStrongKey(true);
        cfg::set('encryption', 'key', $key);

        return $key;
    }

    public static function isKeyStrong(): bool
    {
        return XChaCha20Util::checkIfStrongKey(cfg::get('encryption', 'key'), true);
    }

    public static function getKey(): string
    {
        return cfg::get('encryption', 'key');
    }
}
