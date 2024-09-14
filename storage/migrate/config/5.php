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

use MythicalSystemsFramework\Managers\Settings as settings;

settings::setSetting('smtp', 'enabled', 'false');
settings::setSetting('smtp', 'host', '127.0.0.1');
settings::setSetting('smtp', 'port', '465');
settings::setSetting('smtp', 'secure', 'ssl');
settings::setSetting('smtp', 'username', 'example@mythicalsystems.xyz');
settings::setSetting('smtp', 'password', 'examplePasswordBlaBlaBla');
settings::setSetting('smtp', 'fromMail', 'example@mythicalsystems.xyz');
