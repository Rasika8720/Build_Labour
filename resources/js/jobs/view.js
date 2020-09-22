import Vue from 'vue';

import LoadJob from '../components/job/LoadJob';
import CompanySummary from '../components/job/CompanySummary';
import JobDetails from '../components/job/JobDetails';
import JobRequirements from '../components/job/JobRequirements';
import JobResponsibilities from '../components/job/JobResponsibilities';
import JobAds from '../components/job/JobAds';
import Ads from '../components/common/Ads';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: "#app",
    components: {
        LoadJob,
        CompanySummary,
        JobDetails,
        JobRequirements,
        JobResponsibilities,
        JobAds,
        Ads,
        Alert,
        Navigation,
    },
});