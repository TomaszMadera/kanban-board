<?php

use Dotenv\Dotenv;
use KanbanBoard\Application;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

$root = __DIR__ . '/../';
$dotenv = Dotenv::createImmutable($root);
$dotenv->safeLoad();

$filesystemAdapter = new Local($root);
$filesystem        = new Filesystem($filesystemAdapter);

$router = require_once __DIR__ . '/../includes/routes.php';
$app    = new Application($router, $filesystem);

return $app;
