<?php

namespace KanbanBoard\Services;

use KanbanBoard\Helpers\ArrayHelper;
use KanbanBoard\Helpers\EnvironmentHelper;
use KanbanBoard\Models\Issue;
use KanbanBoard\Models\Milestone;

final class BoardService
{
    private static ?BoardService $instance = null;

    private GithubService $githubService;
    private array $repositories;

    private function __construct()
    {
        $this->githubService = GithubService::getInstance();
        $this->repositories = explode('|', EnvironmentHelper::get('GH_REPOSITORIES'));
    }

    private function __clone()
    {
    }

    public static function getInstance(): BoardService
    {
        if (BoardService::$instance === null) {
            BoardService::$instance = new BoardService();
        }

        return BoardService::$instance;
    }

    public function getBoard()
    {
        $milestones = [];
        foreach ($this->repositories as $repository) {
            foreach ($this->githubService->getMilestones($repository) as $milestone) {
                $milestones[$milestone['title']] = $milestone;
                $milestones[$milestone['title']]['repository'] = $repository;
            }
        }

        ksort($milestones);

        foreach ($milestones as $name => $milestone)
        {
            $issues = $this->getAndPrepareIssues($milestone['repository'], $milestone['number']);
            $percent = Milestone::getCompletionPercentage($milestone['closed_issues'], $milestone['open_issues']);
            if($percent)
            {
                $milestones[] = array(
                    'milestone' => $name,
                    'url' => $milestone['html_url'],
                    'progress' => $percent,
                    'queued' => $issues['queued'] ?? [],
                    'active' => $issues['active'] ?? [],
                    'completed' => $issues['completed'] ?? []
                );
            }
        }

        return $milestones;
    }

    private function getAndPrepareIssues(string $repository, int $milestoneId): array
    {
        $issues = $this->githubService->getIssuesToMilestone($repository, $milestoneId);
        $parsedIssues = $this->parseIssues($issues);
        $this->sortIssues($parsedIssues);
        
        return $parsedIssues;
    }

    private function parseIssues(array $issues): array
    {
        $parsed = [];

        foreach ($issues as $issue) {
            if (ArrayHelper::hasValue($issue, 'pull_request')) {
                continue;
            }

            $issueModel = new Issue($issue);
            $key = $issueModel->getState();
            $parsed[$key][] = $issueModel->parse();
        }

        return $parsed;
    }

    private function sortIssues(array &$issues): void
    {
        $canSort = $issues['queued'] ?? false;

        if ($canSort) {
            usort(
                $issues['queued'],
                function ($a, $b) {
                    return count($a['paused']) - count($b['paused']) === 0
                        ? strcmp($a['title'], $b['title'])
                        : count($a['paused']) - count($b['paused']);
                }
            );
        }
    }
}
