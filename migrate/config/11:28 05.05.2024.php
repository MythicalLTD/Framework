<?php
use MythicalSystemsFramework\Managers\ConfigManager as cfg;
$file = __DIR__ . '/../../settings.json';
if (!file_exists($file)) {
    file_put_contents($file, '{}');
    cfg::add("app", "name", "MythicalSystems");
    cfg::add("app", "logo", "https://avatars.githubusercontent.com/u/117385445");
    cfg::add("app", "timezone", "Europe/Bucharest");
    cfg::add("app", "theme", "default");
    cfg::add("app", "lang", "en_US");
    cfg::add("app", "maintenance", "false");

    cfg::add("seo", "title", "MythicalSystems");
    cfg::add("seo", "description", "MythicalSystems is a framework for building web applications.");
    cfg::add("seo", "keywords", "MythicalSystems, web applications, framework");

    cfg::add("framework", "version", "1.0.1");
    cfg::add("framework", "branch", "develop");

    cfg::add("database", "host", "127.0.0.1");
    cfg::add("database", "port", "3306");
    cfg::add("database", "username", "");
    cfg::add("database", "password", "");
    cfg::add("database", "name", "");

    cfg::add("encryption", "method", "MythicalCore");
    cfg::add("encryption", "key", "");
} else {
    $newFile = __DIR__ . '/../../old_settings.json';
    rename($file, $newFile);
}