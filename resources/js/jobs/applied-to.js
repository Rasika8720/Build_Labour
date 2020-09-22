import Vue from 'vue';

import UserJobSummary from '../components/job/UserJobSummary';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: "#app",
    components: {
        UserJobSummary,
        Alert,
        Navigation,
    },
});
