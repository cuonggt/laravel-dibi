import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from '@/views/Dashboard';
import TableDetail from '@/views/TableDetail';
import TableData from '@/views/TableData';
import TableStructure from '@/views/TableStructure';
import TableInfo from '@/views/TableInfo';
import Error403 from '@/views/403';
import Error404 from '@/views/404';
import Error500 from '@/views/500';

Vue.use(Router);

const router = new Router({
    scrollBehavior,
    base: window.config.base,
    mode: 'history',
    routes: [
        {
            name: 'dashboard',
            path: '/',
            component: Dashboard,
            props: true,
        },
        {
            path: '/tables/:tableName',
            component: TableDetail,
            props: true,
            children: [
                {
                    path: '/',
                    component: TableData,
                    name: 'tables.data',
                    props: true,
                },
                {
                    path: 'structure',
                    component: TableStructure,
                    name: 'tables.structure',
                    props: true,
                },
                {
                    path: 'info',
                    component: TableInfo,
                    name: 'tables.info',
                    props: true,
                },
            ],
        },
        {
        name: '403',
            path: '/403',
            component: Error403,
        },
        {
            name: '404',
            path: '/404',
            component: Error404,
        },
        {
            name: '500',
            path: '/500',
            component: Error500,
        },
        {
            name: 'catch-all',
            path: '*',
            component: Error404,
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

