<?php

session_start();

use KanbanBoard\Application;

$root   = __DIR__ . '/../';

/* Init router and request */
$router = require_once $root . 'includes/routes.php';

/* Init application */
$app    = new Application($root, $router);

return $app;
