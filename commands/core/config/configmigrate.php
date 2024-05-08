<?php 
use MythicalSystemsFramework\Cli\Colors as color;
class configmigrateCommand
{
    public function execute()
    {
        $migratedCount = 0;
        $migratedFiles = [];

        $mdirectory = __DIR__.'/../../../migrate/config/';
        $mifiles = scandir($mdirectory);

        $migratedFilePath = __DIR__.'/../../../caches/migrated_files.txt';
        if (file_exists($migratedFilePath)) {
            $migratedFiles = file($migratedFilePath, FILE_IGNORE_NEW_LINES);
        }

        foreach ($mifiles as $mfiletom) {
            if ($mfiletom !== '.' && $mfiletom !== '..' && !in_array($mfiletom, $migratedFiles)) {
                $filePath = $mdirectory . $mfiletom;
                if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
                    include $filePath;
                    $migratedCount++;
                    $migratedFiles[] = $mfiletom;
                }
            }
        }
        file_put_contents($migratedFilePath, implode(PHP_EOL, $migratedFiles));

        echo color::translateColorsCode("&fMigration completed. Migrated &e$migratedCount &ffiles.");
    }
}