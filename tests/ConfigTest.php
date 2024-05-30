<?php

use PHPUnit\Framework\TestCase;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class ConfigTest extends TestCase
{
    /**
     * Test whether cfg::get returns a non-null value.
     */
    public function testGET()
    {
        $section = 'app';
        $key = 'name';
        $configValue = cfg::get($section, $key);

        // Assert
        $this->assertNotNull($configValue, 'The cfg::get method returned a null value.');
    }

    /**
     * Test that cfg::set correctly sets a new value.
     * 
     * @dataProvider setMethodDataProvider
     */
    public function testSetMethodSetsNewValue($section, $key, $expectedValue)
    {
        // Arrange
        $oldValue = cfg::get($section, $key);

        // Act
        cfg::set($section, $key, $expectedValue);
        $actualValue = cfg::get($section, $key);

        // Assert
        $this->assertEquals($expectedValue, $actualValue, 'The cfg::set method did not update the value correctly.');

        // Clean up
        cfg::set($section, $key, $oldValue);
    }

    /**
     * Data provider for testSetMethodSetsNewValue.
     */
    public static function setMethodDataProvider()
    {
        return [
            ['app', 'name', 'MythicalSystemsT'],
            // Add more test cases as needed
        ];
    }
}