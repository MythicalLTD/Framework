<?php

use MythicalSystemsFramework\Managers\Settings as settings;

settings::setSetting('smtp', 'enabled', 'false');
settings::setSetting('smtp', 'host', '127.0.0.1');
settings::setSetting('smtp', 'port', '465');
settings::setSetting('smtp', 'secure', 'ssl');
settings::setSetting('smtp', 'username', 'example@mythicalsystems.xyz');
settings::setSetting('smtp', 'password', 'examplePasswordBlaBlaBla');
settings::setSetting('smtp', 'fromMail', 'example@mythicalsystems.xyz');
