<?php

use MythicalSystems\Api\Api as api;

api::init();
api::allowOnlyPOST();

if (isset($_POST['email']) && isset($_POST['password'])) {
}
