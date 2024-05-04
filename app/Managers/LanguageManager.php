<?php 
namespace MythicalSystemsFramework\Managers;
use Exception;

class LanguageManager {
    
    /**
     * Get the langauge from the file
     * 
     * @return mixed
     * @throws Exception
     */
    public function getLang() : mixed {
       try {
        $langConfig = ConfigManager::get('app','lang');
        if ($langConfig == null) {
            $langFilePath = __DIR__ . '/../lang/' . $langConfig . '.php';
            if (file_exists($langFilePath)) {
                return include($langFilePath);
            } else {
                die('Please use a valid language file.');
            }
        } else {
            die("Language not found in config!");
        }
       } catch (Exception $e) {
            die("Error: " . $e->getMessage());
       }
    }

    /**
     * Get all available languages
     * 
     * @return array
     */
    public function getAllAvailableLanguages() : array {
        $langFiles = scandir(__DIR__ . '/../lang/');
        $langFiles = array_diff($langFiles, array('..', '.'));
        return $langFiles;
    }
} 