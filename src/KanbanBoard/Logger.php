<?php

namespace KanbanBoard;

use Monolog\ErrorHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Logger extends \Monolog\Logger
{
    const LOG_PATH = __DIR__ . '/../storage/log';

    const ERROR_LOG_NAME = 'kanban-board-error';

    private static array $loggers = [];

    public function __construct(string $instanceName = 'kanban-board-debug', array $config = null)
    {
        parent::__construct($instanceName);

        if (empty($config)) {
            $path = self::LOG_PATH;
            $config = [
                'file'  => "{$path}/{$instanceName}.log",
                'level' => Level::Debug
            ];
        }

        $this->pushHandler(new StreamHandler($config['file'], $config['level']));
    }

    /**
     * Initializes or returns existing logger by its instance name.
     *
     * @param  string     $instanceName
     * @param  array|null $config
     *
     * @return \Monolog\Logger
     */
    public static function getInstance(string $instanceName = 'kanban-board-debug', array $config = null): \Monolog\Logger
    {
        if (empty(static::$loggers[$instanceName])) {
            static::$loggers[$instanceName] = new static($instanceName, $config);
        }

        return static::$loggers[$instanceName];
    }

    /**
     * Initializes default error log.
     *
     * @return void
     */
    public static function handleAppErrors(): void
    {
        $instanceName = static::ERROR_LOG_NAME;

        static::$loggers[$instanceName] = new static($instanceName);
        static::$loggers[$instanceName]->pushHandler(new FirePHPHandler());
        ErrorHandler::register(static::$loggers[$instanceName]);
    }
}
