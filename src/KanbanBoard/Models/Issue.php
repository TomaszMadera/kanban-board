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
    private ?array $assignee;
    private ?array $labels;
    private string $closed_at;
    private string $state;

    public function __construct(array $issueArgs)
    {
        foreach ($issueArgs as $arg => $value) {
            $this->{$arg} = $value;
        }
    }

    public function getState(): string
    {
        if ($this->state === 'closed') {
            return 'completed';
        } else {
            if (isset($this->assignee) && count($this->assignee) > 0) {
                return 'active';
            } else {
                return 'queued';
            }
        }
    }

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

    public function parse(): array
    {
        return [
            'id'       => $this->id,
            'number'   => $this->number,
            'title'    => $this->title,
            'body'     => Markdown::defaultTransform($this->body),
            'url'      => $this->html_url,
            'assignee' => isset($this->assigne) && !empty($this->assigne)
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
