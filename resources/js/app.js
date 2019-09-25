import Vue from 'vue';
import router from '@/router';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';
import axios from 'axios';
import Mixins from './mixins';

Vue.use(Antd);

Vue.config.productionTip = false;

Vue.mixin(Mixins);

window.axios = axios;
window.Bus = new Vue({name: 'Bus'});

import './components';

new Vue({
    el: '#dibi',
    router,
});
