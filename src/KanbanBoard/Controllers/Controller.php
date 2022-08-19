<?php

namespace KanbanBoard\Controllers;

use JetBrains\PhpStorm\NoReturn;
use KanbanBoard\Helpers\DebugHelper;
use Mustache_Engine;
use Mustache_Exception_UnknownTemplateException;
use Mustache_Loader_FilesystemLoader;
use Throwable;

abstract class Controller
{
    private const TEMPLATES_DIR = '../views';

    protected Mustache_Engine $mustache;

    /**
     * Renders view by given template name.
     * Optional context data can be passed.
     *
     * @param string $template
     * @param array  $data
     *
     * @return void
     */
    public function render(string $template, array $data = []): void
    {
        $this->loadTemplates();

        try {
            echo $this->mustache->render($template, $data);
        } catch (Mustache_Exception_UnknownTemplateException $e) {
            DebugHelper::printThrowable($e);
        }
    }

    /**
     * Redirects to given url.
     *
     * @param  string $page
     *
     * @return void
     */
    #[NoReturn] public function redirectTo(string $page): void
    {
        header("Location: {$page}");
        exit();
    }

    /**
     * Loads all templates located in defined directory.
     *
     * @return void
     */
    private function loadTemplates(): void
    {
        try {
            $this->mustache = new Mustache_Engine(
                [
                    'loader' => new Mustache_Loader_FilesystemLoader(self::TEMPLATES_DIR),
                ]
            );
        } catch (Throwable $e) {
            DebugHelper::printThrowable($e);
        }
    }
}
