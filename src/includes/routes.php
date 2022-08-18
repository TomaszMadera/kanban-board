<?php

use KanbanBoard\Controllers\BoardController;
use KanbanBoard\Request;
use KanbanBoard\Router;

$request = new Request();
$router = new Router($request);

/* List of available routes before bootstrapping application. */
$router->get('/', [BoardController::class, 'index']);

/* Return router instance with declared routes. */
return $router;
