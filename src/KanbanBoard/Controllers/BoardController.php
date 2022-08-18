<?php

namespace KanbanBoard\Controllers;

use KanbanBoard\Services\BoardService;

class BoardController extends Controller
{
    private BoardService $boardService;

    public function __construct()
    {
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

        $this->render('index', $milestones);
    }
}
