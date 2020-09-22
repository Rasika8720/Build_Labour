import Vue from 'vue';

import Alert from '../components/common/Alert';
import ResetPassword from '../components/auth/passwords/ResetPassword';

new Vue({
    el: "#app",
    components: {
        Alert,
        ResetPassword,
    },
});