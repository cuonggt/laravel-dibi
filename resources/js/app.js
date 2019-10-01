import Vue from 'vue';
import router from '@/router';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import axios from 'axios';
import Mixins from './mixins';
import Toasted from 'vue-toasted';
import lodash from 'lodash';

window._ = lodash;

Vue.use(Toasted, {
    position: 'bottom-right',
    duration: 6000,
});

Vue.use(Antd);

Vue.config.productionTip = false;

Vue.mixin(Mixins);

window.Bus = new Vue({name: 'Bus'});

window.axios = axios.create();

window.axios.interceptors.response.use(
    response => response,
    error => {
        const { status } = error.response;

        // Show the user a 500 error
        if (status >= 500) {
            Bus.$emit('error', error.response.data.message);

            router.push({ name: '500' });
        }

        // Handle Session Timeouts
        if (status === 401) {
            window.location.href = window.config.base;
        }

        // Handle Forbidden
        if (status === 403) {
            router.push({ name: '403' });
        }

        // Handle Not Found
        if (status === 404) {
            router.push({ name: '404' });
        }

        return Promise.reject(error);
    },
);

import './components';

new Vue({
    el: '#dibi',
    router,
    mounted: function() {
        Bus.$on('error', message => {
            this.$toasted.show(message, { type: 'error' });
        });
    },
});
