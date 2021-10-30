<template>
    <div class="card card-default bg-info text-white">
        <div class="card-header"><h3>
            Create Game
            <i v-if="loading" class="fas fa-cog fa-spin"></i>
        </h3></div>

        <div class="card-body text-center">
            <template v-if="loading === false">
                <div class="col mt-3">
                    <form v-on:submit.prevent="startGame">

                        <div class="form-group">
                            <label for="first_player_name">First player</label>
                            <input class="form-control" placeholder="Name" name="first_player_name" type="text" id="first_player_name" v-model="form.first_player_name">
                        </div>
                        <div class="text-danger">
                            <span v-for="error in errors.first_player_name" :key="error">
                                {{ error }}
                            </span>
                        </div>

                        <div class="form-group">
                            <input checked="checked" name="computer" type="checkbox" v-model="form.computer">
                            <label for="computer">Play Against Computer</label>
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
    props: ['loading', 'errors'],
    data(){
        return{
            form: {
                first_player_name: '',
                computer: false,
            }
        }
    },
    methods: {
        startGame() {
            this.$emit('startGame', this.form)
        },
    },
  }
</script>
