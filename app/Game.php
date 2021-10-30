<?php

namespace App;

use App\Services\GameHistoryService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * App\Game
 *
 * @property int $id
 * @property string $first_player_name
 * @property string|null $second_player_name
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\GameRound $gameRoundLatest
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GameRound[] $gameRounds
 * @property-read int|null $game_rounds_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereFirstPlayerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereSecondPlayerName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Game extends Model
{
    public const DEFAULT_SIZE = 3;
    public const FIRST_PLAYER_TYPE = 1;
    public const SECOND_PLAYER_TYPE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_player_name',
        'second_player_name',
        'token',
    ];

    public static function generateToken(): string
    {
        return (string) Str::uuid();
    }

    public static function getPlayerTypes(?int $type = null)
    {
        $types = [
            self::FIRST_PLAYER_TYPE => 'x',
            self::SECOND_PLAYER_TYPE => 'o',
        ];

        if ($type !== null) {
            return $types[$type] ?? null;
        }

        return $types;
    }

    public function getOpponent()
    {
        return strtolower($this->second_player_name);
    }

    public function setToken(): void
    {
        $this->token = self::generateToken();
    }

    public function gameRounds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(GameRound::class, 'game_id', 'id');
    }

    public function countGameRounds(): int
    {
        return $this->hasMany(GameRound::class, 'game_id', 'id')->count();
    }

    public function gameRoundLatest(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GameRound::class, 'game_id', 'id')->latest();
    }

    public function getWinHistory(): string
    {
        $wins = [1 => 0, 2 => 0, 3 => 0];

        $playerType = null;

        $gamehistories = GameHistory::where([
            'game_id' => $this->id,
        ])
        ->orderBy('id', 'asc')
        ->get();

        $rounds = $gamehistories->groupBy('game_round_id');

        foreach ($rounds as $histories) {
            $gameHistories = [];
            $playerHistories = [];
            foreach ($histories as $history) {
                $playerType = $history->player_type;

                $gameHistories[$history->game_row][$history->game_column] = $playerType;
                $playerHistories[$playerType][$history->game_row][$history->game_column] = true;
            }

            $gameHistoryService = new GameHistoryService;
            $horizontalSuccess = $gameHistoryService->getHorizontalSuccess($gameHistories, $playerHistories, self::DEFAULT_SIZE);
            $verticalSuccess = $gameHistoryService->getVerticalSuccess($gameHistories, $playerHistories, self::DEFAULT_SIZE);

            $diagonalRightSuccess = $gameHistoryService->getDiagonalRightSuccess($gameHistories, $playerHistories, self::DEFAULT_SIZE);
            $diagonalLeftSuccess = $gameHistoryService->getDiagonalLeftSuccess($gameHistories, $playerHistories, self::DEFAULT_SIZE);

            $playerWinner = $gameHistoryService->getPlayerWinner(self::DEFAULT_SIZE, $gameHistories, $horizontalSuccess, $verticalSuccess, $diagonalRightSuccess, $diagonalLeftSuccess);

            if (null == $playerWinner) {
                $wins[3]++;
                continue;
            }

            if ($playerWinner) {
                $wins[$playerWinner]++;
                continue;
            }
        }

        return implode(" | ", $wins);
    }
}
