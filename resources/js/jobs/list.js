import Vue from 'vue';

import CreateJob from '../components/job/CreateJob';
import JobSummary from '../components/job/JobSummary';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: "#app",
    components: {
        CreateJob,
        JobSummary,
        Alert,
        Navigation,
    },
});