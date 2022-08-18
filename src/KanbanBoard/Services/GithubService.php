<?php

namespace KanbanBoard\Services;

use Github\AuthMethod;
use Github\Client as GithubClient;
use KanbanBoard\Controllers\GithubAuthentication;
use KanbanBoard\Helpers\EnvironmentHelper;

final class GithubService
{
    private static ?GithubService $instance = null;

    private GithubClient $client;
    private string $account;
    private string $clientSecret;

    private function __construct()
    {
        $this->client = new GithubClient();
        $this->account = EnvironmentHelper::get('GH_ACCOUNT');
        $this->clientSecret = EnvironmentHelper::get('GH_CLIENT_SECRET');
        $this->authenticate();
    }

    public static function getInstance(): GithubService
    {
        if (GithubService::$instance === null) {
            GithubService::$instance = new GithubService();
        }

        return GithubService::$instance;
    }

    public function getMilestones(string $repository): array
    {
        $milestonesApi = $this->client->api('issues')->milestones();

        return $milestonesApi->all($this->account, $repository);
    }

    public function getIssuesToMilestone(string $repository, int $milestoneId): array
    {
        $issueParams = [
            'milestone' => $milestoneId,
            'state' => 'all'
        ];

        return $this->client->api('issue')->all($this->account, $repository, $issueParams);
    }

    public function authenticate(): void
    {
        $authentication = new GithubAuthentication();
        $token = $authentication->login();

        $this->client->authenticate(
            $token,
            $this->clientSecret,
            AuthMethod::ACCESS_TOKEN
        );
    }
}