<?php

namespace MythicalSystemsFramework\Cli\Commands;

use MythicalSystemsFramework\Cli\CommandBuilder;
use MythicalSystemsFramework\Encryption\XChaCha20;

class Encryption extends Command implements CommandBuilder
{
    public static string $description = 'A command to manage the encryption.';

    public static function execute(bool $isFrameworkCommand, array $args): void
    {
        echo self::log_info('');
        echo self::log_info('&c1.&7 Show the encryption key.');
        echo self::log_info('&c2.&7 Generate a new encryption key.');
        echo self::log_info('&c3.&7 Decrypt a string.');
        echo self::log_info('&c4.&7 Encrypt a string.');
        echo self::log_info('&c5.&7 Exit');
        echo self::log_info('');
        $option = readline('Select an option: ');
        switch ($option) {
            case '1':
                self::show();
                break;
            case '2':
                self::generate();
                break;
            case '3':
                self::decrypt();
                break;
            case '4':
                self::encrypt();
                break;
            case '5':
                self::exit();
                break;
            default:
                echo 'Invalid option selected.';
                break;
        }
    }

    public static function show(): void
    {
        echo self::log_info('The encryption key is: ' . XChaCha20::getKey());
    }

    public static function generate(): void
    {
        Rebuild::db();
    }

    public static function decrypt(): void
    {
        $string_to_decrypt = readline('Enter the string to decrypt: ');
        echo self::log_info('The decrypted string is: ' . XChaCha20::decrypt($string_to_decrypt));
    }

    public static function encrypt(): void
    {
        $string_to_encrypt = readline('Enter the string to encrypt: ');
        echo self::log_info('The encrypted string is: ' . XChaCha20::encrypt($string_to_encrypt));
    }
}
