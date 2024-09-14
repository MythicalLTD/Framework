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

namespace MythicalSystemsFramework\Encryption;

use MythicalSystems\Utils\XChaCha20 as XChaCha20Util;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class XChaCha20 implements Types
{
    /**
     * Decrypt a string.
     *
     * @param string $text_encrypted the text to decrypt
     *
     * @return string the decrypted string
     */
    public static function decrypt(string $text_encrypted): string
    {
        global $event; // This is a global variable that is used to emit events.
        $text_decrypted = XChaCha20Util::decrypt($text_encrypted, self::getKey(), true);
        $event->emit('xchacha20.OnDecrypt', [$text_encrypted, $text_decrypted]);

        return $text_decrypted;
    }

    /**
     * Encrypt a string.
     *
     * @return string the encrypted string
     */
    public static function encrypt(string $text_decrypted): string
    {
        global $event; // This is a global variable that is used to emit events.
        $text_encrypted = XChaCha20Util::encrypt($text_decrypted, self::getKey(), true);
        $event->emit('xchacha20.OnEncrypt', [$text_decrypted, $text_encrypted]);

        return $text_encrypted;
    }

    /**
     * Generate a new key for the encryption.
     */
    public static function generateKey(): string
    {
        global $event; // This is a global variable that is used to emit events.

        $new_key = XChaCha20Util::generateStrongKey(true);
        cfg::set('encryption', 'key', $new_key);
        $event->emit('xchacha20.OnGenerateKey', [$new_key]);

        return $new_key;
    }

    /**
     * Check if the key is strong.
     */
    public static function isKeyStrong(): bool
    {
        return XChaCha20Util::checkIfStrongKey(self::getKey(), true);
    }

    /**
     * Get the key from the configuration.
     */
    public static function getKey(): string
    {
        return cfg::get('encryption', 'key');
    }
}
