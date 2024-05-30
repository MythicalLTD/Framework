<?php
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

$renderer->assign("app_logo", cfg::get("app", "logo"));
$renderer->assign("app_name", cfg::get("app", "name"));


?>