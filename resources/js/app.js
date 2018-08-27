import Vue from 'vue';
import _ from 'lodash';
import axios from 'axios';
import numeral from 'numeral';
import router from './router';
import App from './components/App.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

library.add(fas);

Vue.component('font-awesome-icon', FontAwesomeIcon);

window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js').default;

require('bootstrap');

Vue.prototype.$http = axios.create();

window.Bus = new Vue({name: 'Bus'});

Vue.config.errorHandler = function (err, vm, info) {
    console.error(err);
};

Vue.filter('str_limit', function (value, limit = 100, end = '...') {
    if (typeof value !== 'string') {
        return value;
    }

    if (value.length < limit) {
        return value;
    }

    return value.slice(0, limit).trim() + end;
});

Vue.mixin({
    methods: {
        formatNumber(number, format = '0,0') {
            return numeral(number).format(format);
        }
    }
});

new Vue({
    el: '#root',

    router,

    /**
     * The component's data.
     */
    data() {
        return {}
    },

    render: h => h(App),
});
