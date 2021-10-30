<?php

namespace App\Components;

interface WinnerCheckerInterface
{
    /**
     * Checks if a player won the match
     *
     * @param  array $board
     * @param  int   $player
     * @return bool
     */
    public function check(array $board, int $player): bool;
}
