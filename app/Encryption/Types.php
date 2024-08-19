<?php

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
