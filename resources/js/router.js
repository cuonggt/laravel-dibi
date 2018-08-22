import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

export default new Router({
    mode: 'history',
    base: '/dibi/',
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            path: '/dashboard',
            component: require('./pages/Dashboard.vue'),
        },
        {
            path: '/tables/:table',
            component: require('./pages/Tables/Table.vue'),
            props: true,
            children: [
                {
                    path: '/',
                    redirect: 'rows',
                },
                {
                    path: 'rows',
                    component: require('./pages/Tables/Rows.vue'),
                    name: 'tables.show.rows',
                    props: true,
                },
                {
                    path: 'columns',
                    component: require('./pages/Tables/Columns.vue'),
                    name: 'tables.show.columns',
                    props: true,
                },
                {
                    path: 'info',
                    component: require('./pages/Tables/Info.vue'),
                    name: 'tables.show.info',
                    props: true,
                },
            ],
        },
    ],
});
