<?php

namespace KanbanBoard\Services;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use Github\AuthMethod;
use Github\Client as GithubClient;
use KanbanBoard\Application;
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
        $pool = new FilesystemCachePool(Application::$application->filesystem);
        $pool->setFolder('storage/cache/github-api-cache');
        $this->client->addCache($pool);

        $this->account = EnvironmentHelper::get('GH_ACCOUNT');
        $this->clientSecret = EnvironmentHelper::get('GH_CLIENT_SECRET');
        $this->authenticate();
    }

    private function __clone()
    {
    }

    public static function getInstance(): GithubService
    {
        if (GithubService::$instance === null) {
            GithubService::$instance = new GithubService();
        }

        return GithubService::$instance;
    }

    /**
     * Retrieves milestones from repository by its name.
     *
     * @param  string $repository
     *
     * @return array
     */
    public function getMilestones(string $repository): array
    {
        $milestonesApi = $this->client->api('issues')->milestones();

        return $milestonesApi->all($this->account, $repository);
    }

    /**
     * Retrieve milestones by given repository names.
     *
     * @param  array $repositories
     *
     * @return array
     */
    public function getMilestonesByRepositories(array $repositories): array
    {
        $milestones = [];

        foreach ($repositories as $repository) {
            foreach ($this->getMilestones($repository) as $milestone) {
                $milestones[$milestone['title']] = $milestone;
                $milestones[$milestone['title']]['repository'] = $repository;
            }
        }

        ksort($milestones);

        return $milestones;
    }

    /**
     * Retrieves milestone issues from repository by its name and milestone ID.
     *
     * @param  string $repository
     * @param  int    $milestoneId
     *
     * @return array
     */
    public function getIssuesToMilestone(string $repository, int $milestoneId): array
    {
        $issueParams = [
            'milestone' => $milestoneId,
            'state' => 'all'
        ];

        return $this->client->api('issue')->all($this->account, $repository, $issueParams);
    }

    /**
     * Authenticates GitHub user.
     *
     * @return void
     */
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