<?php 
use MythicalSystems\Api\Api as api;
use MythicalSystemsFramework\Kernel\Logger;
use MythicalSystemsFramework\Kernel\LoggerTypes;
use MythicalSystemsFramework\Kernel\LoggerLevels;

api::init();


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    api::OK("Showing you latest logs", [
        "logs" => Logger::getAllSortedByDate(LoggerTypes::OTHER, LoggerLevels::OTHER,10)
    ]);
}