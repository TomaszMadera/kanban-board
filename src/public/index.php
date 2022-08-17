<?php

require '../vendor/autoload.php';

use Dotenv\Dotenv;
use KanbanBoard\Application;
use KanbanBoard\Controllers\Authentication;
use KanbanBoard\Controllers\Github;
use KanbanBoard\Helpers\EnvironmentHelper;

$dotenv = Dotenv::createImmutable('../');
$dotenv->safeLoad();

$repositories = explode('|', EnvironmentHelper::get('GH_REPOSITORIES'));
$authentication = new Authentication();
$token = $authentication->login();

$github = new Github($token, EnvironmentHelper::get('GH_ACCOUNT'));
$board = new Application($github, $repositories, array('waiting-for-feedback'));
$data = $board->board();
$m = new Mustache_Engine(array(
	'loader' => new Mustache_Loader_FilesystemLoader('../views'),
));
echo $m->render('index', array('milestones' => $data));
