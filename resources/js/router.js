import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from './views/Dashboard.vue';
import TableDetails from './views/TableDetails.vue';
import SqlQuery from './views/SqlQuery.vue';

const router = createRouter({
    history: createWebHistory(window.Dibi.path),
    scrollBehavior,
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
                currentTable: window.Dibi.informationSchema.tables.find(table => table.tableName == route.params.tableName),
            }),
        },
        {
            name: 'catch-all',
            path: '/:pathMatch(.*)*',
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
        return { el: to.hash };
    }

    const matched = router.resolve(to).matched;
    const component = matched.length > 0 ? matched[matched.length - 1].components.default : null;

    if (component && component.scrollToTop === false) {
        return {};
    }

    return { left: 0, top: 0 };
}
