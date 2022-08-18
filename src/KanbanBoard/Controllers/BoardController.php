<?php

namespace KanbanBoard\Controllers;

use KanbanBoard\Services\BoardService;

class BoardController extends Controller
{
    private BoardService $boardService;

    public function __construct()
    {
        /* Should be DI, but I've decided not to implement it and not to get it from composer for this app. */
        $this->boardService = BoardService::getInstance();
    }

    /**
     * Returns index page.
     *
     * @return void
     */
    public function index(): void
    {
        $milestones = $this->boardService->getBoard();

        $this->render('index', ['milestones' => $milestones]);
    }
}
