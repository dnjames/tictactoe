require('./bootstrap');

window.Vue = require('vue');

const App = require('./components/App.vue').default;

const app = new Vue({
  el: '#app',
  components: {
    'app': App
  }
});
