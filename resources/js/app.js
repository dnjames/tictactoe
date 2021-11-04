require('./bootstrap');

window.Vue = require('vue').default;

const App = require('./components/App.vue').default;

const app = new Vue({
  el: '#app',
  components: {
    'app': App
  }
});
