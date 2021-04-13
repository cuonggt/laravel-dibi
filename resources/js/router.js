import Vue from 'vue';
import Router from 'vue-router';
import Dashboard from '@/views/Dashboard';
import TableDetails from '@/views/TableDetails';

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
            path: '/tables/:tableName',
            component: TableDetails,
            name: 'tables-show',
            props: true,
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

