import Vue from 'vue';
import router from '@/router';
import axios from 'axios';
import Base from './base';
import Toasted from 'vue-toasted';

Vue.use(Toasted, {
    position: 'bottom-right',
    duration: 6000,
});

Vue.config.productionTip = false;

Vue.mixin(Base);

window.Bus = new Vue({name: 'Bus'});

window.axios = axios.create();

window.axios.interceptors.response.use(
    response => response,
    error => {
        if (! error.response) {
            return Promise.reject(error);
        }

        const { status } = error.response;

        // Show the user a 500 error
        if (status >= 500) {
            Bus.$emit('error', error.response.data.message);
        }

        // Handle Session Timeouts
        if (status === 401) {
            window.location.href = window.Dibi.path;
        }

        if (status === 403 || status === 404) {
            window.location.href = '/' + status.toString();
        }

        return Promise.reject(error);
    },
);

new Vue({
    el: '#dibi',
    router,
    mounted: function() {
        Bus.$on('error', message => {
            this.$toasted.show(message, { type: 'error' });
        });
    },
});
