<?php

namespace KanbanBoard;

use KanbanBoard\Helpers\ArrayHelper;
use Michelf\Markdown;

class Application {

	public function __construct($github, $repositories, $pausedLabels = [])
	{
		$this->github = $github;
		$this->repositories = $repositories;
		$this->pausedLabels = $pausedLabels;
	}

	public function board()
	{
		$ms = array();
		foreach ($this->repositories as $repository)
		{
			foreach ($this->github->milestones($repository) as $data)
			{
				$ms[$data['title']] = $data;
				$ms[$data['title']]['repository'] = $repository;
			}
		}

		ksort($ms);
        $milestones = [];
		foreach ($ms as $name => $data)
		{
			$issues = $this->issues($data['repository'], $data['number']);
			$percent = $this->getMilestoneCompletionPercentage($data['closed_issues'], $data['open_issues']);
			if($percent)
			{
				$milestones[] = array(
					'milestone' => $name,
					'url' => $data['html_url'],
					'progress' => $percent,
					'queued' => $issues['queued'] ?? [],
					'active' => $issues['active'] ?? [],
					'completed' => $issues['completed'] ?? []
				);
			}
		}

		return $milestones;
	}

	private function issues(string $repository, int $milestoneId): array
    {
		$issues = $this->github->issues($repository, $milestoneId);
		$parsedIssues = $this->parseIssues($issues);

        $canSort = $parsedIssues['queued'] ?? false;

        if ($canSort) {
            usort(
                $parsedIssues['queued'],
                function ($a, $b) {
                    return count($a['paused']) - count($b['paused']) === 0
                        ? strcmp($a['title'], $b['title'])
                        : count($a['paused']) - count($b['paused']);
                }
            );
        }

		return $parsedIssues;
	}

    private function parseIssues(array $issues): array
    {
        $parsed = [];

        foreach ($issues as $issue) {
            if (isset($issue['pull_request'])) {
                continue;
            }

            $key = $this->getIssueState($issue);

            $parsed[$key][] = [
                'id'       => $issue['id'],
                'number'   => $issue['number'],
                'title'    => $issue['title'],
                'body'     => Markdown::defaultTransform($issue['body']),
                'url'      => $issue['html_url'],
                'assignee' => (is_array($issue) && array_key_exists('assignee', $issue) && !empty($issue['assignee']))
                    ? $issue['assignee']['avatar_url'] . '?s=16'
                    : null,
                'paused'   => $this->isIssuePaused($issue),
                'progress' => $this->getMilestoneCompletionPercentage(
                    substr_count(strtolower($issue['body']), '[x]'),
                    substr_count(strtolower($issue['body']), '[ ]')
                ),
                'closed'   => $issue['closed_at']
            ];
        }

        return $parsed;
    }

	private function getIssueState(array $issue): string
	{
        if ($issue['state'] === 'closed') {
            return 'completed';
        } else {
            if (ArrayHelper::hasValue($issue, 'assignee') && count($issue['assignee']) > 0) {
                return 'active';
            } else {
                return 'queued';
            }
        }
	}

    private function isIssuePaused($issue): array
    {
        if (ArrayHelper::hasValue($issue, 'labels')) {
            return array_column(
                array_filter($issue['labels'], fn ($label) => in_array($label['name'], $this->pausedLabels)),
                'name'
            );
        }

        return [];
    }

    private function getMilestoneCompletionPercentage(int $complete, int $remaining): array
    {
        $total = $complete + $remaining;

        $percent = $total > 0 ? round($complete / $total * 100) : 0;

        return [
            'total'     => $total,
            'complete'  => $complete,
            'remaining' => $remaining,
            'percent'   => $percent
        ];
    }
}
