<template>
    <div class="card card-default">
        <div class="card-header bg-info text-white text-center"><h3>
            {{ game.first_player_name }} ({{ game.first_player_type }}) | {{ game.second_player_name }} ({{ game.second_player_type }})
            <i v-if="loading" class="fas fa-cog fa-spin"></i>
        </h3></div>

        <div class="card-body text-center">
            <div v-if="(prepareData && prepareData.gameHistories.length < 1)">
                Start the game
            </div>
            <div v-if="(game.isFullGameField || prepareData.gameOver)">

                <div class="text-center">
                    The game is over
                </div>

                <div v-if="prepareData.playerWinner">
                    <div class="text-center f-size-2">
                        Winner  "{{ prepareData.playerWinner == game.firstPlayerType ? game.first_player_name : game.second_player_name }}"
                    </div>
                </div>
                <div v-if="!prepareData.playerWinner">
                    <div class="text-center f-size-2">
                        A draw
                    </div>
                </div>
            </div>

            <div v-if="(!game.isFullGameField && !prepareData.gameOver)">
                <div v-if="(prepareData.playerType && prepareData.playerType == game.firstPlayerType)">
                    <div class="d-flex align-items-center justify-content-center">
                        <div>Your Turn&nbsp;</div>
                        <div class="mb-2 f-size-2" :id="game.opponent">{{ game.second_player_type }}</div>
                    </div>
                </div>

                <div v-if="(prepareData.playerType && prepareData.playerType == game.secondPlayerType)">
                    <div class="d-flex align-items-center justify-content-center">
                        <div>Your Turn&nbsp;</div>
                        <div class="mb-2 f-size-2">{{ game.first_player_type }}</div>
                    </div>
                </div>
            </div>

            <div class="ttt-content mt-3">
                <div v-for="row in game.gameSizeArray" :key="row" class="d-flex justify-content-center ttt-row">
                    <div v-for="col in game.gameSizeArray" :key="col" class="align-self-center ttt-col">
                        <div v-if="(prepareData.gameHistories[row] && prepareData.gameHistories[row][col])" class="ttt-element">
                            <div v-if="(prepareData.horizontalSuccess[row])" class="line"></div>
                            <div v-if="(prepareData.verticalSuccess[col])" class="line rotate-90"></div>
                            <div v-if="(prepareData.diagonalRightSuccess[row] && prepareData.diagonalRightSuccess[row][col])" class="line rotate-135"></div>
                            <div v-if="(prepareData.diagonalLeftSuccess[row] && prepareData.diagonalLeftSuccess[row][col])" class="line rotate-45"></div>
                            <input name="board[]" type="hidden" :value="(prepareData.gameHistories[row][col] == game.firstPlayerType ? game.first_player_type : game.second_player_type)">
                            {{ prepareData.gameHistories[row][col] == game.firstPlayerType ? game.first_player_type : game.second_player_type }}
                        </div>
                        <div v-else>
                            <div v-if="(!prepareData.gameOver)" class="ttt-element">
                                <form v-on:submit.prevent="handleMove(game.gameBoardArray[row+''+col])" :id="'form'+game.gameBoardArray[row+''+col]">
                                    <input name="game_id" type="hidden" :value="game.id">
                                    <input name="game_round_id" type="hidden" :value="game.round">
                                    <input name="game_row" type="hidden" :value="row">
                                    <input name="game_column" type="hidden" :value="col">
                                    <input name="board[]" type="hidden" :value="null">
                                    <input v-if="(!prepareData.playerType)" name="player_type" type="hidden" :value="game.firstPlayerType">
                                    <input v-if="(prepareData.playerType && prepareData.playerType == game.firstPlayerType)" name="player_type" type="hidden" :value="game.secondPlayerType">
                                    <input v-if="(prepareData.playerType && prepareData.playerType == game.secondPlayerType)" name="player_type" type="hidden" :value="game.firstPlayerType">

                                    <button class="btn btn-link btn-block" :id="game.gameBoardArray[row+''+col]"></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="(prepareData.playerType && prepareData.playerType == game.firstPlayerType)">
                <span v-if="computerOpponent(game.opponent)"></span>
            </div>
        </div>

        <br/>
        <div class="col-sm text-center pb-3">
            <form v-on:submit.prevent="newRound()" :id="'newround'">
                <input name="game_id" type="hidden" :value="game.id">

                <button class="btn btn-primary">Play Again</button>
            </form>
        </div>
        <div class="text-center">
            <a href="#" class="btn btn-light" @click="showGames"><i class="fas fa-back"></i>Back to History</a>
        </div>
    </div>
</template>

<script>
export default {
    props: ['game', 'loading', 'prepareData'],
    data(){
        return{
            form: {},
        }
    },
    methods: {
        handleMove(id) {
            let inputs = document.getElementById("form"+id).getElementsByTagName("input");
            let form = {};

            Array.from(inputs).forEach(function (input, index) {
                let name = input.name;
                let value = input.value;
                form[name] = value;
            });

            this.$emit('handleMove', form)
        },
        computerOpponent(opponent) {
            if (opponent == 'computer') {
                setTimeout(()=>{
                    let board = Array.from(document.getElementsByName('board[]')).map(el => el.value.length < 1 ? null : el.value.toUpperCase());
                    // randomMove();
                    computerTurn(board);
                }, 1000);
            }
        },
        newRound() {
            let inputs = document.getElementById("newround").getElementsByTagName("input");
            let form = {};

            Array.from(inputs).forEach(function (input, index) {
                let name = input.name;
                let value = input.value;
                form[name] = value;
            });

            this.$emit('newRound', form);
        },
        showGames() {
            this.$emit('showGames');
        }
    }
}

let turn;
const PLAYER="X", AI="O";

const SOLUTIONS = [
    [0, 1, 2],
    [3, 4, 5],
    [6, 7, 8],
    [0, 3, 6],
    [1, 4, 7],
    [2, 5, 8],
    [0, 4, 8],
    [2, 4, 6]
];

function randomMove() {
    let position = Math.floor(Math.random() * 10);
    console.log(position);
    let choice = document.getElementById(position);
    console.log(choice);
    if (choice) {
      choice.click();
      return true;
    }
    randomMove();
}

function addArea(newBoard, index, symbol) {
    if (newBoard[index] == null) {
      newBoard[index] = symbol;
      return true;
    } else {
      return false;
    }
}

//using minmax algorithm
let computerTurn = (newBoard) => {
    let bestScore = -Infinity;
    let bestRoute;
    let symbol = AI;
    for (let x = 0; x < 9; x++) {
      let tempBoard = newBoard.slice();
      let result = addArea(tempBoard, x, symbol);
      if (result) {
        let score = minimaxAlgo(tempBoard, true, 0);
        if (score > bestScore) {
          bestScore = score;
          bestRoute = x;
        }
      }
    }

    let choice = document.getElementById(bestRoute);
    if (choice) {
      choice.click();
      return true;
    }
};

let scores = [10, -10, 0];

function minimaxAlgo(tempBoard, isMaximizing, depth) {
    let result = checkWinner(tempBoard);
    if (result != false) {
      if (!isMaximizing) {
        return scores[result.winner] + depth;
      }else {
        return scores[result.winner] - depth;
      }
    }
    turn = !isMaximizing ? 0 : 1;
    let bestScore = !isMaximizing? -Infinity: Infinity;
    for (let i = 0; i < 9; i++) {
        // Is the spot available?
        let symbol = !isMaximizing ? AI : PLAYER;
        let newBoard = tempBoard.slice();
        let result = addArea(newBoard, i, symbol);
        if (result) {
          let score = minimaxAlgo(newBoard, !isMaximizing, depth + 1);
          bestScore = (!isMaximizing) ? Math.max(score, bestScore) : Math.min(score, bestScore);
      }
    }
    return bestScore;
  }

function checkWinner(tempBoard) {
    let team = (turn == 1) ? PLAYER : AI;
    //changing the plays into a string so that we could use indexOf to check for every element
    let plays = tempBoard.reduce((accu, curr, index) => (curr === team) ? accu.concat(index) : accu, []);

    //flattening the value so that we could search through the string with the solutions array
    for (let [index, win] of SOLUTIONS.entries()) {
      if (win.every(element => plays.indexOf(element) != -1)) {
        return {winner: turn, winCombos:index};
      }
    }

    //if there's no winner and the remaining empty cell is zero, its a draw so return 2.
    if (getEmptyCellsSize(tempBoard) == 0) {
      return {winner: 2, winCombos: null};
    }
    return false;
}

function getEmptyCellsSize (tempBoard) {
    return tempBoard.reduce((accu, curr, index)=> (curr === null)? accu.concat(index): accu, []).length;
}
</script>
