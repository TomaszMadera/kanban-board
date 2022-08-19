<?php

namespace KanbanBoard\Controllers;

use KanbanBoard\Helpers\ArrayHelper;
use KanbanBoard\Helpers\EnvironmentHelper;

class GithubAuthentication
{
    // TODO refactor
	private string $clientId;
	private string $clientSecret;

	public function __construct()
	{
		$this->clientId = EnvironmentHelper::get('GH_CLIENT_ID');
		$this->clientSecret = EnvironmentHelper::get('GH_CLIENT_SECRET');
	}

	public function logout()
	{
		unset($_SESSION['gh-token']);
	}

	public function login()
	{
		$token = null;
		if(array_key_exists('gh-token', $_SESSION)) {
			$token = $_SESSION['gh-token'];
		}
		else if(ArrayHelper::hasValue($_GET, 'code')
			&& ArrayHelper::hasValue($_GET, 'state')
			&& $_SESSION['redirected'])
		{
			$_SESSION['redirected'] = false;
			$token = $this->_returnsFromGithub($_GET['code']);
		}
		else
		{
			$_SESSION['redirected'] = true;
			$this->_redirectToGithub();
		}
		$this->logout();
		$_SESSION['gh-token'] = $token;
		return $token;
	}

	private function _redirectToGithub()
	{
		$url = 'Location: https://github.com/login/oauth/authorize';
		$url .= '?client_id=' . $this->clientId;
		$url .= '&scope=repo';
		$url .= '&state=LKHYgbn776tgubkjhk';
		header($url);
		exit();
	}

	private function _returnsFromGithub($code)
	{
		$url = 'https://github.com/login/oauth/access_token';
		$data = array(
			'code' => $code,
			'state' => 'LKHYgbn776tgubkjhk',
			'client_id' => $this->clientId,
			'client_secret' => $this->clientSecret);
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => "Content-type: application/x-www-form-urlencoded\r\n",
				'content' => http_build_query($data),
			),
		);
		$context = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		if ($result === FALSE)
			die('Error');
		$result = explode('=', explode('&', $result)[0]);
		array_shift($result);
		return array_shift($result);
	}
}
