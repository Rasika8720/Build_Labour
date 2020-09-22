import Vue from 'vue';
import Router from 'vue-router';
import Routes from '@/router/routes';
import Api from '@/api';

Vue.use(Router);

var routes = new Router({
    routes: Routes,
    mode: 'history',
});

export default routes
