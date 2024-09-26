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

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Managers\Settings;

global $router, $renderer;

$router->add('/manifest.webmanifest', function (): void {
    Api::init();
    $app_name = Settings::getSetting('app', 'name');
    $app_logo = Settings::getSetting('app', 'logo');
    $seo_description = Settings::getSetting('seo', 'description');
    $keywords = Settings::getSetting('seo', 'keywords');
    $keywords_array = array_map('trim', explode(',', $keywords));
    $lang = Settings::getSetting('app', 'lang');
    $lang = preg_replace('/_[A-Z]+$/', '', $lang);
    $custom = [
        'name' => $app_name,
        'short_name' => $app_name,
        'start_url' => '/',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'background_color' => '#161931',
        'theme_color' => '#161931',
        'icons' => [
            [
                'src' => $app_logo,
                'sizes' => '192x192',
                'type' => 'image/png',
            ],
            [
                'src' => $app_logo,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ],
            [
                'src' => $app_logo,
                'sizes' => '512x512',
                'type' => 'image/png',
            ],
        ],
        'description' => $seo_description,
        'lang' => $lang,
        'categories' => $keywords_array,
    ];
    exit(json_encode($custom, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
});
