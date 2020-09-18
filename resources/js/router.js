import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from '@/views/Dashboard';
import TableDetail from '@/views/TableDetail';
import TableData from '@/views/TableData';
import TableStructure from '@/views/TableStructure';
import TableInfo from '@/views/TableInfo';

Vue.use(Router);

const router = new Router({
    scrollBehavior,
    base: window.Dibi.path,
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
                    path: '',
                    name: 'tables',
                    redirect: 'data',
                },
                {
                    path: 'data',
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

