<?php

require __DIR__ . '/../vendor/autoload.php';

use KanbanBoard\Application;
use KanbanBoard\Controllers\Authentication;
use KanbanBoard\Controllers\Github;
use KanbanBoard\Helpers\Utilities;

// TODO ONLY FOR TEST PURPOSES, MOVE OUTSIDE GIT
putenv('GH_CLIENT_ID=');
putenv('GH_CLIENT_SECRET=');
putenv('GH_ACCOUNT=');
putenv('GH_REPOSITORIES=');

$repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new Authentication();
$token = $authentication->login();
$github = new Github($token, Utilities::env('GH_ACCOUNT'));
$board = new Application($github, $repositories, array('waiting-for-feedback'));
$data = $board->board();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));
echo $m->render('index', array('milestones' => $data));
