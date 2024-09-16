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

namespace MythicalSystemsFramework\Encryption;

interface Types
{
    /**
     * Encrypt a text.
     *
     * @param string $text the text to encrypt
     *
     * @return string the encrypted text
     */
    public static function encrypt(string $text): string;

    /**
     * Decrypt an encrypted text.
     *
     * @param string $text the text to decrypt
     *
     * @return string the decrypted text
     */
    public static function decrypt(string $text): string;

    /**
     * Generate a key.
     */
    public static function generateKey(): string;

    /**
     * Is the key strong can it be used?
     */
    public static function isKeyStrong(): bool;

    /**
     * Get the key.
     */
    public static function getKey(): string;
}
