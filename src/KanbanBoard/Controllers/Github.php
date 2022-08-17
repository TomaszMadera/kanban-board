<?php

namespace KanbanBoard\Controllers;

use Github\AuthMethod;
use Github\Client as GithubClient;
use KanbanBoard\Helpers\EnvironmentHelper;

class Github
{
    private $client;
    private $milestone_api;
    private $account;

    public function __construct($token, $account)
    {
        $this->account = $account;
        $this->client = new GithubClient();
        $this->client->authenticate(
            $token,
            EnvironmentHelper::get('GH_CLIENT_SECRET'),
            AuthMethod::ACCESS_TOKEN
        );
        $this->milestone_api = $this->client->api('issues')->milestones();
    }

    public function milestones($repository)
    {
        return $this->milestone_api->all($this->account, $repository);
    }

    public function issues($repository, $milestone_id)
    {
        $issue_parameters = array('milestone' => $milestone_id, 'state' => 'all');
        return $this->client->api('issue')->all($this->account, $repository, $issue_parameters);
    }
}