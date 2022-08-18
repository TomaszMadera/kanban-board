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
    const VERSION = '1.0.2';

	public function __construct(public Router $router)
	{
	}

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
