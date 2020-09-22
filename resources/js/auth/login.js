import Vue from 'vue';

import Login from '../components/auth/Login';
import Alert from '../components/common/Alert';

new Vue({
    el: "#app",
    components: {
        Login,
        Alert,
    },
});