import Vue from 'vue';

import Register from '../components/auth/Register';
import Alert from '../components/common/Alert';

new Vue({
    el: "#app",
    components: {
        Register,
        Alert,
    },
});