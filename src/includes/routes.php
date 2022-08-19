<?php

use KanbanBoard\Controllers\BoardController;
use KanbanBoard\Controllers\GithubAuthenticationController;
use KanbanBoard\Request;
use KanbanBoard\Router;

$request = new Request();
$router = new Router($request);

/* List of available routes before bootstrapping application. */
$router->get('/', [BoardController::class, 'index']);
$router->get('/logout', [GithubAuthenticationController::class, 'logout']);

/* Return router instance with declared routes. */
return $router;
