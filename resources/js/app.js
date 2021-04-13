import Vue from 'vue';
import router from '@/router';
import axios from 'axios';
import Base from './base';
import _ from 'lodash';

Vue.config.productionTip = false;

Vue.mixin(Base);

window.Bus = new Vue({name: 'Bus'});

window.axios = axios.create();
window._ = _;

const token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

// Components
Vue.component('field-value', require('./components/FieldValue.vue').default);
Vue.component('loader', require('./components/Loader.vue').default);

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
