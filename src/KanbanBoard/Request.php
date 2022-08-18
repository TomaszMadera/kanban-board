<?php

namespace KanbanBoard;

class Request
{
    /**
     * Parses server REQUEST_URI to get full route without additional params.
     *
     * @return string
     */
    public function getUri(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $queryParamsPosition = strpos($path, '?');

        if (!$queryParamsPosition) {
            return $path;
        }

        return substr($path, 0, $queryParamsPosition);
    }

    /**
     * Returns current request method type. E.g. GET, POST.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }
}
