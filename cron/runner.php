<?php 
try {
    if (file_exists(__DIR__.'/../vendor/autoload.php')) {
        require (__DIR__.'/../vendor/autoload.php');
    } else {
        die('Hello, it looks like you did not run: "composer install --no-dev --optimize-autoloader". Please run that and refresh the page');
    }
} catch (Exception $e) {
    die('Hello, it looks like you did not run: composer install --no-dev --optimize-autoloader Please run that and refresh');
}

/**
 * MythicalSystems Framework Cron File
 * 
 * This is the main file that adds crons to our framework
 * 
 * Please do not edit anything from here and only add files inside: jobs
 */

$jobsDirectory = __DIR__.'/jobs';
$files = scandir($jobsDirectory);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        require $jobsDirectory.'/'.$file;
    }
}
