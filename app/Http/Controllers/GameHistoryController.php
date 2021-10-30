<?php

namespace App\Http\Controllers;

use App\Game;
use App\Services\GameHistoryService;
use App\Http\Requests\StoreGameHistory;

class GameHistoryController extends Controller
{
    protected $gameHistoryService;

    public function __construct(GameHistoryService $gameHistoryService)
    {
        $this->gameHistoryService = $gameHistoryService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGameHistory $request)
    {
        $gameHistory = $this->gameHistoryService->create($request->validated());

        $game = $gameHistory->game;
        $round = ($game->gameRoundLatest->id ?? null);
        $gameCountHistories = $this->gameHistoryService->countByGame($game, $round);

        $game->first_player_type = $game::getPlayerTypes(Game::FIRST_PLAYER_TYPE);
        $game->second_player_type = $game::getPlayerTypes(Game::SECOND_PLAYER_TYPE);
        $game->firstPlayerType = Game::FIRST_PLAYER_TYPE;
        $game->secondPlayerType = Game::SECOND_PLAYER_TYPE;
        $game->gameSize = Game::DEFAULT_SIZE;
        $game->gameSizeArray = range(1, Game::DEFAULT_SIZE);
        $game->gameCountHistories = $gameCountHistories;
        $game->isFullGameField = $this->gameHistoryService->isFullGameField($gameCountHistories, Game::DEFAULT_SIZE);
        $game->opponent = $game->getOpponent();
        $game->round = $round;
        $game->gameBoardArray = [11 => 0, 12 => 1, 13 => 2, 21 => 3, 22 => 4, 23 => 5, 31 => 6, 32 => 7, 33 => 8];

        return response()->json([
            'current' => 'game',
            'game' => $game,
            'token' => $game->token,
            'round' => $round,
            'prepareData' => $this->gameHistoryService->getPreparedData($game, $round, Game::DEFAULT_SIZE)
        ]);
    }
}
