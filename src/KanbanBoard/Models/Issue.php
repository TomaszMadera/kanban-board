<?php

namespace KanbanBoard\Models;

use KanbanBoard\Enums\PausedLabelsEnum;
use Michelf\Markdown;

class Issue
{
    private int $id;
    private int $number;
    private string $title;
    private string $body;
    private string $html_url;
    private string $state;
    private ?array $assignee;
    private ?array $labels;
    private ?string $closed_at;

    public function __construct(array $issueArgs)
    {
        /* Simple model construct from given array args. */
        foreach ($issueArgs as $arg => $value) {
            $this->{$arg} = $value;
        }
    }

    /**
     * Checks issue state and assignee and returns custom status.
     *
     * @return string
     */
    public function getState(): string
    {
        if ($this->state === 'closed') {
            $state = 'completed';
        } else {
            if (isset($this->assignees) && count($this->assignees) > 0) {
                $state = 'active';
            } else {
                $state = 'queued';
            }
        }

        return $state;
    }

    /**
     * Checks if issue has assigned one of paused labels.
     * If yes, returns array of matching paused labels.
     *
     * @return array
     */
    public function isPaused(): array
    {
        if (isset($this->labels)) {
            return array_column(
                array_filter(
                    $this->labels,
                    fn ($label) => in_array($label['name'], PausedLabelsEnum::values())
                ),
                'name'
            );
        }

        return [];
    }

    /**
     * Parses issue to array.
     *
     * @return array
     */
    public function parse(): array
    {
        return [
            'id'       => $this->id,
            'number'   => $this->number,
            'title'    => $this->title,
            'body'     => Markdown::defaultTransform($this->body),
            'url'      => $this->html_url,
            'assignee' => isset($this->assignee) && !empty($this->assignee)
                ? $this->assignee['avatar_url'] . '?s=16'
                : null,
            'paused'   => $this->isPaused(),
            'progress' => Milestone::getCompletionPercentage(
                substr_count(strtolower($this->body), '[x]'),
                substr_count(strtolower($this->body), '[ ]')
            ),
            'closed'   => $this->closed_at
        ];
    }
}
