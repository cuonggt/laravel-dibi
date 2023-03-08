import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from './views/Dashboard';
import TableDetails from './views/TableDetails';
import SqlQuery from './views/SqlQuery';

Vue.use(Router);

const router = new Router({
    scrollBehavior,
    base: window.Dibi.path,
    mode: 'history',
    routes: [
        {
            path: '/',
            redirect: '/dashboard',
        },
        {
            name: 'dashboard',
            path: '/dashboard',
            component: Dashboard,
            props: true,
        },
        {
            name: 'sql-query',
            path: '/sql-query',
            component: SqlQuery,
            props: true,
        },
        {
            path: '/tables/:tableName',
            component: TableDetails,
            name: 'tables-show',
            props: route => ({
                tableName: route.params.tableName,
                currentTable: Dibi.informationSchema.tables.find(table => table.tableName == route.params.tableName),
            }),
        },
        {
            name: 'catch-all',
            path: '*',
            redirect: () => {
                window.location.href = '/404';
            },
        },
    ],
});

export default router;

function scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
        return savedPosition;
    }

    if (to.hash) {
        return { selector: to.hash };
    }

    const [component] = router.getMatchedComponents({ ...to }).slice(-1);

    if (component && component.scrollToTop === false) {
        return {};
    }

    return { x: 0, y: 0 };
}

