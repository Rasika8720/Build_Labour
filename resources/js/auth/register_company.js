import Vue from 'vue';

import RegisterCompany from '../components/auth/RegisterCompany';
import Alert from '../components/common/Alert';

new Vue({
    el: "#app",
    components: {
        RegisterCompany,
        Alert,
    },
});