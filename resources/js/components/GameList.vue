<template>
    <div class="card card-default bg-primary text-white">
        <div class="card-header"><h3>
            History
            <i v-if="loading" class="fas fa-cog fa-spin"></i>
        </h3></div>

        <div class="list-group list-group-flush">
            <template v-if="loading === false">
                <div v-if="!games.length">
                    <p class="alert alert-info">No matches created yet. Be the first one!</p>
                </div>
                <table class="table">
                    <thead>
                    <tr class="text-white">
                        <th scope="col">#</th>
                        <th scope="col">The players</th>
                        <th scope="col">Number of games</th>
                        <th scope="col">Player 1 | Player 2 | Draw</th>
                        <th scope="col"></th>
                    </tr>
                    </thead>
                    <tbody v-for="game in games" :key="game.id" class="list-history-item">
                            <tr>
                                <th scope="row">{{ game.id }}</th>
                                <td>{{ game.first_player_name }} | {{ game.second_player_name }}</td>
                                <td>{{ game.countGameRounds }}</td>
                                <td>{{ game.getWinHistory }}</td>
                                <td>
                                    <button type="button" class="btn btn-danger" @click="removeGame(game.id)">
                                        Remove
                                    </button>
                                </td>
                            </tr>
                    </tbody>
                </table>
            </template>
            <a v-else class="list-group-item text-body text-center">
                <i class="fas fa-cog fa-spin"></i> Loading...
            </a>
        </div>

        <div class="card-body text-center">
            <button type="button" class="btn btn-success btn-raised btn-lg" @click="createGame()">
                Start New Game
            </button>
        </div>
    </div>
</template>

<script>
  export default {
    props: ['games', 'loading'],
    methods: {
      showMatch(id) {
        this.$emit('showMatch', id)
      },
      createGame() {
        this.$emit('createGame')
      },
      removeGame(id) {
        this.$emit('removeGame', id)
      },
    }
  }
</script>
