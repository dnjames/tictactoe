<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <template v-if="inGames">
                    <game-list :games="games" :loading="loading" @showGame="showGame"
                                @createGame="createGame" @removeGame="removeGame"/>
                </template>
                <template v-if="createGamePage">
                    <create-game :loading="loading" :errors="errors" @startGame="startGame"/>
                </template>
                <template v-if="editGamePage">
                    <edit-game :loading="loading" :errors="errors" :game="game" @updateGame="updateGame"/>
                </template>
                <template v-if="inGame">
                    <game :game="game" :loading="loading" :prepareData="prepareData" @handleMove="handleMove" @newRound="newRound" @showGames="showGames"/>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
  import api from '../api'
  import GameList from './GameList'
  import CreateGame from './CreateGame'
  import EditGame from './EditGame'
  import Game from './Game'

  const
    GAMES = 'games',
    CREATEGAME = 'creategame',
    EDITEGAME = 'editgame',
    GAME = 'game',
    RELOAD_TIME = 100000

  export default {
    components: {GameList, CreateGame, EditGame, Game},
    data: function () {
      return {
        games: {},
        game: {},
        prepareData: null,
        loading: true,
        errors: {},
        current: GAMES,
      }
    },
    computed: {
      inGames() {
        return this.current === GAMES;
      },
      createGamePage() {
        return this.current === CREATEGAME;
      },
      editGamePage() {
        return this.current === EDITEGAME;
      },
      inGame() {
        return this.current === GAME;
      }
    },
    methods: {
      showGame(id) {
        this.loading = true;
        api.game({id: id}).then(({data}) => {
          this.game = data;
          this.loading = false;
          this.current = GAME;
        })
      },
      showGames() {
        this.loading = true;
        this.current = GAMES;
        api.games().then(({data}) => {
          this.games = data;
          this.loading = false;
        })

        setInterval(() => {
          api.games().then(({data}) => {
            this.games = data;
          })
        }, RELOAD_TIME);
      },
      handleMove(form) {
        let that = this;
        that.loading = false;
        api.move(form).then(({data}) => {
            that.current = data.current;
            that.game = data.game;
            that.prepareData = data.prepareData;
        })
      },
      newRound(form) {
        let that = this;
        that.loading = false;
        api.newround(form).then(({data}) => {
            that.current = data.current;
            that.game = data.game;
            that.prepareData = data.prepareData;
        }).catch((error) => {
            if (error.response && error.response.data && error.response.data.errors) {
                that.errors = error.response.data.errors;
            }
        })
      },
      createGame() {
        let that = this;
        that.loading = false;
        that.current = CREATEGAME;
        that.game = {};
        that.prepareData = null;
      },
      startGame(form) {
        let that = this;
        that.loading = false;
        api.create(form).then(({data}) => {
            that.current = data.current;
            that.game = data.game;
            that.prepareData = data.prepareData;
        }).catch((error) => {
            if (error.response && error.response.data && error.response.data.errors) {
                that.errors = error.response.data.errors;
            }
        })
      },
      updateGame(info) {
        let that = this;
        that.loading = false;
        api.update(info.game_id, info.form).then(({data}) => {
            that.current = data.current;
            that.game = data.game;
            that.prepareData = data.prepareData;
        }).catch((error) => {
            if (error.response && error.response.data && error.response.data.errors) {
                that.errors = error.response.data.errors;
            }
        });
      },
      removeGame(id) {
        let that = this;
        that.loading = true;
        api.destroy({id: id}).then(({data}) => {
          that.games = data;
          that.loading = false;
          that.current = GAMES;
        })
      }
    },
    mounted() {
        this.showGames();
    }
  }
</script>
