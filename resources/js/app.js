import { createApp } from 'vue';
import axios from 'axios';
import _ from 'lodash';
import mitt from 'mitt';
import Toast from 'vue-toastification';
import 'vue-toastification/dist/index.css';
import router from './router';
import Base from './base';
import App from './App.vue';
import { registerComponents } from './components';

window._ = _;
window.Bus = mitt();

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
            Bus.emit('error', error.response.data.message);
        }

        return Promise.reject(error);
    },
);

const app = createApp(App);
app.use(router);
app.use(Toast, { position: 'bottom-right', timeout: 6000 });
app.mixin(Base);
registerComponents(app);
app.mount('#dibi');
