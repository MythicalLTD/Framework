<?php

use MythicalSystemsFramework\Api\Api;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\User\UserHelper;
use MythicalSystemsFramework\Web\Template\Engine;
use MythicalSystemsFramework\User\UserDataHandler;

global $router;

$router->add('/admin/settings/(.*)', function ($category): void {
    global $router, $event, $renderer;
    $template = 'admin/settings/edit.twig';
    if (isset($_COOKIE['token']) === false) {
        exit(header('location: /auth/login'));
    }

    $user = new UserHelper($_COOKIE['token'], $renderer);
    UserDataHandler::requireAuthorization($renderer, $_COOKIE['token']);

    if (
        !UserDataHandler::hasPermission($_COOKIE['token'], 'mythicalframework.admin.settings.view') ||
        !UserHelper::hasPermission($_COOKIE['token'], 'mythicalframework.admin.settings.edit')
    ) {
        exit(header('location: /errors/403'));
    }

    $category_list = [
        "general",
        "mails",
        "cloudflare",
        "seo",
        "custom"
    ];

    if (!in_array($category, $category_list)) {
        exit(header('location: /errors/404'));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            Api::init();

            $input = json_decode(file_get_contents('php://input'), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                exit(header('location: /errors/400'));
            }

            if (!isset($input['category'], $input['name'], $input['value'])) {
                exit(header('location: /errors/400'));
            }

            $category = $input['category'];
            $name = $input['name'];
            $value = $input['value'];

            Settings::updateSetting($category, $name, $value, true);
            Api::OK("Setting updated successfully.", [
                "category" => $category,
                "name" => $name,
                "value" => $value
            ]);
        } catch (Exception $e) {
            Api::InternalServerError("There was an error updating the setting.", null);
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $renderer->addGlobal('category_name', $category);
        $renderer->addGlobal('page_name', 'Settings');
        $timezones = DateTimeZone::listIdentifiers();
        $langs = array_filter(scandir(__DIR__ . '/../../../../storage/lang'), function ($file) {
            return pathinfo($file, PATHINFO_EXTENSION) === 'yml';
        });
        $langs = array_map(function ($file) {
            return pathinfo($file, PATHINFO_FILENAME);
        }, $langs);
        $renderer->addGlobal('langs', $langs);
        $renderer->addGlobal('timezones', $timezones);

        $themes = array_filter(scandir(__DIR__ . '/../../../../storage/themes'), function ($file) {
            return is_dir(__DIR__ . '/../../../../storage/themes/' . $file) && $file !== '.' && $file !== '..';
        });
        $renderer->addGlobal('themes', $themes);

        $renderer->addFunction(new Twig\TwigFunction('ucFirst', function (string $word) {
            return ucfirst($word);
        }));

        Engine::registerAlerts($renderer, $template);
        exit($renderer->render($template));
    } else {
        exit(header('location: /dashboard'));
    }
});
