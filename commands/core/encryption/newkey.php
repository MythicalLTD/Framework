<?php

use MythicalSystemsFramework\Cli\Colors as color;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class newkeyCommand
{
    public function execute()
    {
        if (cfg::get('encryption', 'key') == '') {
            $this->generate();
        } else {
            echo color::translateColorsCode('&4&lWARNING: &fA key already exists. Do you want to generate a new one?&o');
            echo color::translateColorsCode('&4&lWARNING: &fGenerating a new key will make all encrypted data unreadable. &o');
            echo color::translateColorsCode('&4&lWARNING: &fType &ey&f to continue or &en&f to exit:');
            $confirm = readline();
            if (strtolower($confirm) === 'y') {
                $this->generate();
            } else {
                exit(color::translateColorsCode('&fExiting...&o'));
            }
        }
    }

    public function generate()
    {
        $key = 'mythicalcore_' . bin2hex(random_bytes(64 * 32));
        cfg::set('encryption', 'key', $key);
        echo color::translateColorsCode('&fKey generated successfully!&o');
    }
}
