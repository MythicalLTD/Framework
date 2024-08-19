<?php

namespace MythicalSystemsFramework\Tests;

use PHPUnit\Framework\TestCase;
use MythicalSystems\Utils\EncryptionHandler;

class EncryptionTest extends TestCase
{
    /**
     * The encryption key.
     */
    public static string $key = 'AAAAB3NzaC1yc2EAAAADAQABAAABAQDZHXFFVqeRQk42pViMsxfQhIrQBm7LcmW1sazqkgkCgoOVcW4OiCoaH0P9Zf5HjqdJ9RJocnJz8qKQQUiCFxuxt8qJiHMoqf+Mu8KgOs6ixo0SLiH2QakAZ0Nm46WB+VjXmLHkxZ9tw/f2M9dGI5Ky0M0TvsKSXS0v8crXLBBE3Fa+gao/34Cyqim1ZhCopVIjTtRNSZwx0CzHcYGhegl04+nIksCYg7RH56CTH5j1NZX8enJ7T5lx9sl8YIde6qJu7tD0nsfZFTRxwUzLvfGmFIQ9/96BNThB7aK806T8Tr+amKsPcYEc3Il6LifoPztYS6pYtbwBl3eMm0mchNgJ';
    /**
     * The data to encrypt.
     */
    public static string $data = 'Hello World, this is a unit test for the encryption handler :)!';
    /**
     * The encrypted data.
     */
    public static string $data_e = 'iaatrbFTpenTr5Wlg6atqrRhqrdhsmG3r6q1YrW2t85ovrW4duXNt3HQopXiz9nB3OfUcdCq4LWu0qlsnZZ4';

    public function testDecryption(): void
    {
        $decryptedData = EncryptionHandler::decrypt(self::$data_e, self::$key);
        $this->assertEquals(self::$data, $decryptedData);
    }

    public function testEncryption(): void
    {
        $encryptedData = EncryptionHandler::encrypt(self::$data, self::$key);
        $this->assertEquals(self::$data_e, $encryptedData);
    }
}
