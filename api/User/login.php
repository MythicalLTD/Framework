<?php 

use MythicalSystems\Api\Api as api;
use MythicalSystemsFramework\Managers\ConfigManager as cfg;

api::init();
api::allowOnlyPOST();

if (isset($_POST['email']) && isset($_POST['password'])) {

}

?>