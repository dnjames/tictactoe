<template>
    <div class="card card-default bg-info text-white">
        <div class="card-header"><h3>
            Add Second Player
            <i v-if="loading" class="fas fa-cog fa-spin"></i>
        </h3></div>

        <div class="card-body text-center">
            <template v-if="loading === false">
                <div class="col mt-3">
                    <form v-on:submit.prevent="updateGame(game)">

                        <div class="form-group">
                            <label for="second_player_name">Second player</label>
                            <input class="form-control" placeholder="Name" name="second_player_name" type="text" id="second_player_name" v-model="form.second_player_name">
                        </div>
                        <div class="text-danger">
                            <span v-for="error in errors.second_player_name" :key="error">
                                {{ error }}
                            </span>
                        </div>


                        <div class="form-group">
                            <button class="btn btn-primary">Start</button>
                        </div>

                    </form>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
  export default {
    props: ['game', 'loading', 'errors'],
    data(){
        return {
            form: {
                second_player_name: '',
            }
        }
    },
    methods: {
        updateGame(game) {
            let form = this.form;
            let game_id = game.id;
            this.$emit('updateGame', {game_id, form})
        },
    },
  }
</script>
