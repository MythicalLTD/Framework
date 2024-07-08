<?php

namespace MythicalSystemsFramework\Managers;

use Exception;
use MythicalSystemsFramework\Kernel\Logger as logger;
use MythicalSystemsFramework\Kernel\LoggerLevels;
use MythicalSystemsFramework\Kernel\LoggerTypes;

class LanguageManager
{

    /**
     * Get the language from the file
     * 
     * @return mixed
     */
    public static function getLang(): mixed
    {
        try {
            $fallback_lang = __DIR__ . '/../../lang/en_US.php';
            if (file_exists($fallback_lang)) {
                $langConfig = SettingsManager::get('app', 'lang');
                if (!$langConfig == "") {
                    $langFilePath = __DIR__ . '/../../lang/' . $langConfig . '.php';
                    if (file_exists($langFilePath)) {
                        return include($langFilePath);
                    } else {
                        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::LANGUAGE, "Language file is invalid!!");
                        return include($fallback_lang);
                    }
                } else {
                    Logger::log(LoggerLevels::CRITICAL, LoggerTypes::LANGUAGE, "Default language file has not been found in the config!!");
                    return include($fallback_lang);
                }
            } else {
                logger::log(LoggerLevels::CRITICAL, LoggerTypes::LANGUAGE, "Fallback language file has not been found!!");
                die("Fallback language file has not been found!! You are missing important files!");
            }
        } catch (Exception $e) {
            Logger::log(LoggerLevels::CRITICAL, LoggerTypes::LANGUAGE, "Error while loading language file: " . $e->getMessage());
            return include($fallback_lang);
        }
    }

    /**
     * Get all available languages
     * 
     * @return array
     */
    public static function getAllAvailableLanguages(): array
    {
        $langFiles = scandir(__DIR__ . '/../../lang/');
        $langFiles = array_diff($langFiles, array('..', '.'));
        return $langFiles;
    }

    /**
     * Log translation key not found
     * 
     * @param string $key Translation key
     * @param string $message The alternative message
     * 
     * @return string The translation key not found message
     */
    public static function logKeyTranslationNotFound($key, $message = "TRANSLATION_KEY_NOT_FOUND"): string
    {
        Logger::log(LoggerLevels::CRITICAL, LoggerTypes::LANGUAGE, "Translation key not found: " . $key);
        return $message;
    }
}
