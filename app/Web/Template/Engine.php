<?php

namespace MythicalSystemsFramework\Web\Template;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;
use MythicalSystemsFramework\Managers\Settings;
use MythicalSystemsFramework\Web\Installer\Installer;
use MythicalSystemsFramework\Managers\LanguageManager;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

class Engine
{
    /**
     * Add the requirements for the template engine.
     */
    public static function getRenderer(?string $cache_dir = null, ?string $theme_dir = null): Environment
    {
        if ($cache_dir == null) {
            define('DIR_TEMPLATE', __DIR__ . '/../../../storage/themes/' . Settings::getSetting('app', 'theme'));
        } else {
            define('DIR_TEMPLATE', $theme_dir);
        }

        if ($theme_dir == null) {
            define('DIR_CACHE', __DIR__ . '/../../../storage/caches');
        } else {
            define('DIR_CACHE', $cache_dir);
        }
        /*
         * Load the template engine
         */

        if (!is_dir(DIR_TEMPLATE)) {
            Installer::showError('The theme directory does not exist!');
        }

        if (!is_dir(DIR_CACHE)) {
            mkdir(DIR_CACHE, 0777, true);
        }

        $loader = new FilesystemLoader(DIR_TEMPLATE);
        $renderer = new Environment($loader, [
            'cache' => DIR_CACHE,
            'auto_reload' => true,
            'debug' => true,
            'charset' => 'utf-8',
        ]);

        self::registerSettings($renderer);
        self::registerConfig($renderer);
        self::registerLanguage($renderer);
        self::registerGlobals($renderer);
        self::registerIsUserValid($renderer);

        return $renderer;
    }

    /**
     * Register the config function.
     */
    public static function registerConfig(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('cfg', function ($section, $key): string {
            return cfg::get($section, $key);
        }));
    }

    /**
     * Register the language function.
     */
    public static function registerLanguage(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('lang', function ($key): string {
            $translations = LanguageManager::getLang();

            return $translations[$key] ?? LanguageManager::logKeyTranslationNotFound($key);
        }));
    }

    /**
     * Register the settings function.
     */
    public static function registerSettings(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('setting', function ($section, $key): string {
            return Settings::getSetting($section, $key);
        }));

        $renderer->addFunction(new TwigFunction('settings', function ($section, $key): string {
            return Settings::getSetting($section, $key);
        }));
    }

    /**
     * Register global values into twig.
     */
    public static function registerGlobals(Environment $renderer): void
    {
        $renderer->addGlobal('php_version', phpversion());
        $renderer->addGlobal('page_name', 'Home');
    }

    /**
     * Register the isUserSessionValid function.
     *
     * This is used so we know what should we display on the page.
     */
    public static function registerIsUserValid(Environment $renderer): void
    {
        $renderer->addFunction(new TwigFunction('isUserSessionValid', function (): bool {
            if (isset($_COOKIE['token'])) {
                return true;
            } else {
                return false;
            }
        }));
    }
}
