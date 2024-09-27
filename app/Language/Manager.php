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

namespace MythicalSystemsFramework\Language;

use Symfony\Component\Yaml\Yaml;
use MythicalSystemsFramework\Plugins\PluginCompilerHelper;
use MythicalSystemsFramework\Managers\Settings as settings;

class Manager
{
    public string $lang_dir = __DIR__ . '/../../storage/lang';
    public array $langs = [];
    public string $default_lang = 'en_US';
    public string $language;

    public function __construct()
    {
        if (!$this->doesLanguageDirectoryExist()) {
            $this->createLanguageDirectory();
        }
        $this->renameYAMLToYML();
        $this->langs = $this->getLangs();
        foreach ($this->langs as $lang) {
            $this->checkSyntax($lang);
        }

        if ($this->doesLanguageExist(settings::getSetting('app', 'lang'))) {
            $this->language = settings::getSetting('app', 'lang');
        } else {
            $this->language = $this->default_lang;
        }
    }

    /**
     * Get a language string.
     *
     * @param string $key Language key
     */
    public function get(string $key): ?string
    {
        $lang_files[] = PluginCompilerHelper::getLanguagePaths();
        $file = $this->lang_dir . '/' . $this->language . '.yml';
        $yaml = Yaml::parseFile($file);
        $keys = explode('.', $key);

        $value = $yaml;
        foreach ($keys as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                if ($lang_files != null) {
                    foreach ($lang_files as $plugin) {
                        if (isset($plugin[0])) {
                            $file_name = $plugin[0];
                            if (file_exists($file_name)) {
                                $yaml = Yaml::parseFile($file_name);
                                $keys = explode('.', $key);
                                $value = $yaml;
                                foreach ($keys as $part) {
                                    if (isset($value[$part])) {
                                        $value = $value[$part];
                                    } else {
                                        return null;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return is_string($value) ? $this->replacePlaceholders($value) : null;
    }

    /**
     * Get the full language file.
     */
    public function getFullFile(): array
    {
        $file = $this->lang_dir . '/' . $this->language . '.yml';

        return Yaml::parseFile($file);
    }

    /**
     * Set a language string.
     *
     * @param string $key Language key
     * @param string $value Language value
     */
    public function set(string $key, string $value): void
    {
        $file = $this->lang_dir . '/' . $this->language . '.yml';
        $yaml = Yaml::parseFile($file);
        $keys = explode('.', $key);

        $value = $yaml;
        foreach ($keys as $part) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                return;
            }
        }
        $yaml[$key] = $value;
        file_put_contents($file, Yaml::dump($yaml));
    }

    /**
     * Get all available languages.
     */
    public function getLangs(): array
    {
        $files = scandir($this->lang_dir);
        $langs = [];
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'yml') {
                $langs[] = $file;
            }
        }

        return $langs;
    }

    /**
     * Replace placeholders in the language string.
     */
    private function replacePlaceholders(?string $replacePlaceholders): ?string
    {
        if ($replacePlaceholders == null) {
            return null;
        }

        $replacePlaceholders = str_replace('%app_name%', settings::getSetting('app', 'name'), $replacePlaceholders);
        $replacePlaceholders = str_replace('%app_logo%', settings::getSetting('app', 'logo'), $replacePlaceholders);
        $replacePlaceholders = str_replace('%app_timezone%', settings::getSetting('app', 'timezone'), $replacePlaceholders);
        $replacePlaceholders = str_replace('%app_url%', settings::getSetting('app', 'url'), $replacePlaceholders);

        $replacePlaceholders = str_replace('%seo_title%', settings::getSetting('seo', 'title'), $replacePlaceholders);
        $replacePlaceholders = str_replace('%seo_description%', settings::getSetting('seo', 'description'), $replacePlaceholders);

        return str_replace('%seo_keywords%', settings::getSetting('seo', 'keywords'), $replacePlaceholders);
    }

    /**
     * Check the syntax of the language file.
     *
     * @param string $lang Language name
     */
    private function checkSyntax(string $lang): void
    {
        if ($this->doesLanguageExist($lang)) {
            $file = $this->lang_dir . '/' . $lang;
            $yaml = Yaml::parseFile($file);
            if ($yaml == null) {
                throw new \Exception('Language file syntax is invalid!');
            }
        } else {
            throw new \Exception('Language file does not exist!');
        }
    }

    /**
     * Does this language exist?
     *
     * @param string $lang Language name
     *
     * @return bool True if yes, false if no!
     */
    private function doesLanguageExist(string $lang): bool
    {
        return in_array($lang, $this->langs);
    }

    /**
     * Rename all YAML files to YML.
     */
    private function renameYAMLToYML(): void
    {
        $files = scandir($this->lang_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'yaml') {
                $new_file = str_replace('.yaml', '.yml', $file);
                rename($this->lang_dir . '/' . $file, $this->lang_dir . '/' . $new_file);
            }
        }
    }

    /**
     * Check if the language directory exists.
     */
    private function doesLanguageDirectoryExist(): bool
    {
        return is_dir($this->lang_dir);
    }

    /**
     * Create the language directory.
     */
    private function createLanguageDirectory(): void
    {
        mkdir($this->lang_dir);
    }
}
