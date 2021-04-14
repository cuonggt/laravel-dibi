import Vue from 'vue';
import axios from 'axios';
import _ from 'lodash';
import PortalVue from 'portal-vue';
import router from '@/router';
import Base from './base';

Vue.config.productionTip = false;

Vue.use(PortalVue);

Vue.mixin(Base);

window.Bus = new Vue({name: 'Bus'});

window.axios = axios.create();
window._ = _;

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Components
Vue.component('x-button', require('./components/Button.vue').default);
Vue.component('x-dialog-modal', require('./components/DialogModal.vue').default);
Vue.component('x-field-value', require('./components/FieldValue.vue').default);
Vue.component('x-input', require('./components/Input.vue').default);
Vue.component('x-label', require('./components/Label.vue').default);
Vue.component('x-loader', require('./components/Loader.vue').default);
Vue.component('x-secondary-button', require('./components/SecondaryButton.vue').default);

// Icons
Vue.component('icon-chevron-left', require('./components/icons/ChevronLeft.vue').default);
Vue.component('icon-chevron-right', require('./components/icons/ChevronRight.vue').default);
Vue.component('icon-cog', require('./components/icons/Cog.vue').default);
Vue.component('icon-database', require('./components/icons/Database.vue').default);
Vue.component('icon-loader', require('./components/icons/Loader.vue').default);
Vue.component('icon-table', require('./components/icons/Table.vue').default);

new Vue({
    el: '#dibi',
    router,
});
