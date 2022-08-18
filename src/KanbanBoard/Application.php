<?php

namespace KanbanBoard;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;
use KanbanBoard\Helpers\DebugHelper;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use vierbergenlars\SemVer\version;

class Application
{
    /**
     * Current application version.
     *
     * @var string
     */
    const VERSION = '2.0.0';

    public static Application $application;

    public Filesystem $filesystem;

    public function __construct(public string $rootPath, public Router $router)
    {
        self::$application = $this;
    }

    /**
     * Runs application instance and listens to requests.
     *
     * @return void
     */
    public function run(): void
    {
        Logger::handleAppErrors();

        $this->initEnvironmentVars();

        $this->initLocalFilesystem();

        $this->router->resolve();
    }

    /**
     * Initializes environment vars by reading .env config file.
     *
     * @return void
     */
    private function initEnvironmentVars(): void
    {
        $dotenv = Dotenv::createImmutable($this->rootPath);

        try {
            $dotenv->load();
        } catch (InvalidPathException $e) {
            DebugHelper::printThrowable($e);
        }
    }

    /**
     * Initializes local filesystem.
     *
     * @return void
     */
    private function initLocalFilesystem(): void
    {
        $filesystemAdapter = new Local($this->rootPath);
        $filesystem        = new Filesystem($filesystemAdapter);

        $this->filesystem = $filesystem;
    }

    /**
     * Returns current application version.
     *
     * @return version
     */
    public function version(): version
    {
        return new version(self::VERSION);
    }
}
