<?php

namespace KanbanBoard\Helpers;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

final class DebugHelper
{
    private function __construct()
    {
    }

    /**
     * Pretties dumped data.
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
        if (isset($_ENV['DEBUG']) && $_ENV['DEBUG']) {
            die("Exception thrown in {$e->getFile()}, line {$e->getLine()}: {$e->getMessage()}");
        } else {
            die();
        }
    }
}
