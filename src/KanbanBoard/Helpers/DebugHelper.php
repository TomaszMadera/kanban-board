<?php

namespace KanbanBoard\Helpers;

use JetBrains\PhpStorm\NoReturn;
use KanbanBoard\Application;
use KanbanBoard\Logger;
use Throwable;

final class DebugHelper
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    /**
     * Dumps and pretties dumped data.
     *
     * @param  mixed $data
     *
     * @return void
     */
    public static function dump(mixed $data): void
    {
        echo '<pre>';
        var_dump($data);
        echo '</pre>';
    }

    /**
     * Stops application and prints exception if DEBUG mode enabled.
     *
     * @param  Throwable $e
     *
     * @return void
     */
    #[NoReturn] public static function printThrowable(Throwable $e): void
    {
        $message = "Exception thrown in {$e->getFile()}, line {$e->getLine()}: {$e->getMessage()}";

        (Logger::getInstance())->debug($message);

        if (isset($_ENV['DEBUG']) && $_ENV['DEBUG']) {
            die($message);
        } else {
            die();
        }
    }

    /**
     * Stops application and prints given error message if DEBUG mode enabled.
     *
     * @param  string $message
     *
     * @return void
     */
    #[NoReturn] public static function die(string $message): void
    {
        (Logger::getInstance())->debug($message);

        if (isset($_ENV['DEBUG']) && $_ENV['DEBUG']) {
            die($message);
        } else {
            die();
        }
    }
}
