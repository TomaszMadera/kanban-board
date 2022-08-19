<?php

namespace KanbanBoard\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JetBrains\PhpStorm\NoReturn;
use KanbanBoard\Helpers\ArrayHelper;
use KanbanBoard\Helpers\DebugHelper;
use KanbanBoard\Helpers\EnvironmentHelper;
use KanbanBoard\Helpers\SessionHelper;
use KanbanBoard\Logger;

class GithubAuthenticationController extends Controller
{
    public const GITHUB_API_URL = 'https://api.github.com';
    public const GITHUB_OAUTH_URL = 'https://github.com/login/oauth';
    public const GITHUB_OAUTH_AUTHORIZE_URI = '/authorize';
    public const GITHUB_OAUTH_ACCESS_TOKEN_URI = '/access_token';

    private Client $httpClient;
    private string $clientId;
    private string $clientSecret;
    private string $scope;
    private string $state;

    public function __construct()
    {
        $this->httpClient   = new Client();
        $this->clientId     = EnvironmentHelper::get('GH_CLIENT_ID');
        $this->clientSecret = EnvironmentHelper::get('GH_CLIENT_SECRET');
        $this->scope        = EnvironmentHelper::get('GH_SCOPE');
        $this->state        = EnvironmentHelper::get('GH_STATE');
    }

    /**
     * Destroys GitHub user session.
     *
     * @return void
     */
    public function logout(): void
    {
        SessionHelper::delete('gh-token');
    }

    /**
     * Set session or check if its already set, then return GitHub access_token.
     *
     * @return string|null
     */
    public function login(): ?string
    {
        if (SessionHelper::exists('gh-token')) {
            return $this->checkToken(SessionHelper::get('gh-token'));
        }

        if (
            ArrayHelper::hasValue($_GET, 'code')
            && ArrayHelper::hasValue($_GET, 'state')
            && SessionHelper::get('redirected')
        ) {
            $token = $this->exchangeCodeForAccessToken($_GET['code']);

            SessionHelper::set('gh-token', $token);

            return $token;
        } else {
            $this->redirectToGitHubLoginPage();
        }
    }

    /**
     * Authorize GitHub user.
     *
     * @return void
     */
    #[NoReturn] private function redirectToGitHubLoginPage(): void
    {
        $queryParams = [
            'client_id' => $this->clientId,
            'scope'     => $this->scope,
            'state'     => $this->state
        ];

        $queryString = http_build_query($queryParams);

        $url = static::GITHUB_OAUTH_URL . static::GITHUB_OAUTH_AUTHORIZE_URI . '?' . $queryString;

        SessionHelper::set('redirected', true);

        $this->redirectTo($url);
    }

    /**
     * Exchanges retrieved code for GitHub access token and returns it.
     *
     * @param  string $code
     *
     * @return string
     */
    private function exchangeCodeForAccessToken(string $code): string
    {
        SessionHelper::set('redirected', false);

        $data = [
            'code'          => $code,
            'state'         => $this->state,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret
        ];

        $options = [
            RequestOptions::FORM_PARAMS => $data,
            RequestOptions::HEADERS => [
                ['Content-Type' => 'application/x-www-form-urlencoded'],
            ]
        ];

        $url = static::GITHUB_OAUTH_URL . static::GITHUB_OAUTH_ACCESS_TOKEN_URI;

        try {
            $result = $this->httpClient->post($url, $options);
        } catch (GuzzleException $e) {
            DebugHelper::printThrowable($e);
        }

        $result = $result->getBody()->getContents();
        $parsedResult = [];
        parse_str($result, $parsedResult);

        return $parsedResult['access_token'];
    }

    /**
     * Checks if token is valid by sending simple GET request to GitHub API.
     *
     * @param string $token
     *
     * @return string|null
     */
    private function checkToken(string $token): ?string
    {
        $options = [
            RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$token}"
            ]
        ];

        $url = static::GITHUB_API_URL . '/user';

        try {
            $this->httpClient->get($url, $options);
        } catch (GuzzleException $e) {
            (Logger::getInstance())->debug($e);

            $this->logout();

            $this->redirectToGitHubLoginPage();
        }

        return $token;
    }
}
