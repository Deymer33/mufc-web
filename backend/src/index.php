<?php

require_once 'router/routes.php';

ini_set('Display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/mufc-web/backend/error_log');


$router = new Router();
$router->handleRequest();

