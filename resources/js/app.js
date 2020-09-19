global.$ = global.jQuery = require('jquery');

// require('./main');
require('./custom');
require('./bootstrap');

require('../assets/vendor/js/bootstrap');

window.Vue = require('vue');

Vue.component('playlist-generator', require('./components/PlaylistGenerator.vue').default);
Vue.component('audio-player', require('./components/AudioPlayer.vue').default)

const app = new Vue({
    el: '#app'
})