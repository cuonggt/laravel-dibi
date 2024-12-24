import Vue from 'vue';
import axios from 'axios';
import _ from 'lodash';
import PortalVue from 'portal-vue';
import Toasted from 'vue-toasted';
import router from './router';
import Base from './base';

Vue.config.productionTip = false;

Vue.use(PortalVue);
Vue.use(Toasted, {
    position: 'bottom-right',
    duration: 6000,
});

window._ = _;
window.Bus = new Vue({ name: 'Bus' });

window.axios = axios.create();

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

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

        return Promise.reject(error);
    },
);

import './components';

Vue.mixin(Base);

new Vue({
    el: '#dibi',
    router,
    mounted() {
        Bus.$on('error', message => {
            this.$toasted.show(message, { type: 'error' });
        });
    },
});
