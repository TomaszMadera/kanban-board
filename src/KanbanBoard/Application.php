<?php

namespace KanbanBoard;

use vierbergenlars\SemVer\version;

class Application
{
    /**
     * Current application version.
     *
     * @var string
     */
    const VERSION = '2.0.0';

	public function __construct(public Router $router)
	{
	}

    /**
     * Runs application instance and listens to requests.
     *
     * @return void
     */
    public function run(): void
    {
        $this->router->resolve();
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
