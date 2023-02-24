import Vue from 'vue';
import axios from 'axios';
import _ from 'lodash';
import PortalVue from 'portal-vue';
import Toasted from 'vue-toasted';
import router from '@/router';
import Base from './base';
import NavList from './components/NavList.vue';

Vue.config.productionTip = false;

Vue.use(PortalVue);
Vue.use(Toasted, {
    position: 'bottom-right',
    duration: 6000,
});

Vue.mixin(Base);

window.Bus = new Vue({ name: 'Bus' });

window.axios = axios.create();
window._ = _;

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

// Components
Vue.component('XButton', require('./components/Button.vue').default);
Vue.component('XDialogModal', require('./components/DialogModal.vue').default);
Vue.component('XFieldValue', require('./components/FieldValue.vue').default);
Vue.component('XInput', require('./components/Input.vue').default);
Vue.component('XLabel', require('./components/Label.vue').default);
Vue.component('XLoader', require('./components/Loader.vue').default);
Vue.component('XSecondaryButton', require('./components/SecondaryButton.vue').default);

// Icons
Vue.component('IconChevronLeft', require('./components/icons/ChevronLeft.vue').default);
Vue.component('IconChevronRight', require('./components/icons/ChevronRight.vue').default);
Vue.component('IconCog', require('./components/icons/Cog.vue').default);
Vue.component('IconDatabase', require('./components/icons/Database.vue').default);
Vue.component('IconLoader', require('./components/icons/Loader.vue').default);
Vue.component('IconTable', require('./components/icons/Table.vue').default);

new Vue({
    el: '#dibi',
    router,
    components: {
        NavList,
    },
    mounted() {
        Bus.$on('error', message => {
            this.$toasted.show(message, { type: 'error' });
        });
    },
});
