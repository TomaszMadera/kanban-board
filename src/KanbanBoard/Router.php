<?php

namespace KanbanBoard;

use KanbanBoard\Helpers\DebugHelper;
use ReflectionClass;
use ReflectionException;

class Router
{
    protected array $routes = [];

    public function __construct(public Request $request)
    {
    }

    /**
     * Registers GET route.
     *
     * @param  string                     $path
     * @param  array|string|callable|null $action
     *
     * @return void
     */
    public function get(string $path, array|string|callable|null $action): void
    {
        $this->routes['get'][$path] = $action;
    }

    /**
     * Resolves incoming request with provided action.
     *
     * @return mixed
     */
    public function resolve(): mixed
    {
        $route = $this->request->getUri();
        $method = $this->request->getMethod();
        $action = $this->routes[$method][$route] ?? false;

        if (!$action) {
            http_response_code(404);
            DebugHelper::die('Action not found.');
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        list($controller, $method) = $action;

        try {
            $reflection = new ReflectionClass($controller);
            $method = $reflection->getMethod($method);

            /* Should be DI, but I've decided not to implement it and not to get it from composer for this app. */
            return $method->invoke($reflection->newInstance());
        } catch (ReflectionException $e) {
            http_response_code(404);
            DebugHelper::printThrowable($e);
        }
    }
}
