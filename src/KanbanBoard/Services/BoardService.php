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

    /**
     * Returns board data.
     *
     * @return array
     */
    public function getBoard(): array
    {
        $milestones = $this->githubService->getMilestonesByRepositories($this->repositories);

        return $this->prepareMilestonesToDisplay($milestones);
    }

    /**
     * Parses retrieved milestones to expected form.
     *
     * @param  array $milestones
     *
     * @return array
     */
    private function prepareMilestonesToDisplay(array $milestones): array
    {
        $parsedMilestones = [];

        foreach ($milestones as $name => $milestone) {
            $issues = $this->getAndPrepareIssues($milestone['repository'], $milestone['number']);
            $percent = Milestone::getCompletionPercentage($milestone['closed_issues'], $milestone['open_issues']);

            $parsedMilestones[] = [
                'milestone' => $name,
                'url' => $milestone['html_url'],
                'progress' => $percent,
                'queued' => $issues['queued'] ?? [],
                'active' => $issues['active'] ?? [],
                'completed' => $issues['completed'] ?? []
            ];
        }

        return $parsedMilestones;
    }

    /**
     * Retrieves milestone issues from GitHub API and parses them.
     *
     * @param  string $repository
     * @param  int    $milestoneId
     *
     * @return array
     */
    private function getAndPrepareIssues(string $repository, int $milestoneId): array
    {
        $issues = $this->githubService->getIssuesToMilestone($repository, $milestoneId);
        $parsedIssues = $this->parseIssues($issues);
        $this->sortIssues($parsedIssues);

        return $parsedIssues;
    }

    /**
     * Parses array of milestone issues.
     *
     * @param  array $issues
     *
     * @return array
     */
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

    /**
     * Sorts issues using reference.
     *
     * @param  array $issues
     *
     * @return void
     */
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
