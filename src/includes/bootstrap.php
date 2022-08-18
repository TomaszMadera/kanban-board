<?php

use Dotenv\Dotenv;
use KanbanBoard\Application;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

$router = require_once __DIR__ . '/../includes/routes.php';
$app = new Application($router);

return $app;
