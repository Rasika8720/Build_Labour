import Home from '@/components/home/Main';

const routes = [

    { path: '/', redirect: '/home' },

    {
        path: '/home', name: 'Home', component: Home,
        meta: {title: 'Home'}
    },

];

export default routes;