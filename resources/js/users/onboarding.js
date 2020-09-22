import Vue from 'vue';

import LoadUser from '../components/profile/LoadUser';
import Onboarding from '../components/onboarding/Onboarding';
import JobAds from '../components/job/JobAds';
import Ads from '../components/common/Ads';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: "#app",
    components: {
        LoadUser,
        Onboarding,
        JobAds,
        Ads,
        Alert,
        Navigation,
    },
});