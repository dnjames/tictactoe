<?php

namespace App\Http\Controllers;

use App\Game;
use App\Services\GameService;
use App\Services\GameHistoryService;
use App\Http\Requests\StoreGame;
use App\Http\Requests\UpdateGame;

class GameController extends Controller
{
    protected $gameService;

    protected $gameHistoryService;

    public function __construct(GameService $gameService, GameHistoryService $gameHistoryService)
    {
        $this->gameService = $gameService;
        $this->gameHistoryService = $gameHistoryService;
    }

    /**
     * Returns a list of games.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return response()->json($this->gameService->listGames());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGame $request)
    {
        if ($request->has('computer') && $request->input('computer') == 'on') {
            $request->merge(['second_player_name' => 'Computer']);
            $game = $this->gameService->createComputer($request->only(['first_player_name', 'second_player_name']));

            return $this->gameInfo($game);
        }
        $game = $this->gameService->create($request->validated());

        return response()->json([
            'current' => 'editgame',
            'game' => $game,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Game  $game
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateGame $request, Game $game)
    {
        $game = $this->gameService->update($game, $request->validated());
        return $this->gameInfo($game);
    }

    /**
     * gameInfo
     *
     * @param  \App\Game $game
     * @return \Illuminate\Http\JsonResponse
     */
    private function gameInfo(Game $game)
    {
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

    /**
     * Deletes the game and returns the new list of games
     *
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $this->gameService->delete($id);
        return response()->json($this->gameService->listGames());
    }
}
