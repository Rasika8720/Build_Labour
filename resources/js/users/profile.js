import Vue from 'vue';

import LoadUser from '../components/profile/LoadUser';
import UserProfile from '../components/profile/UserProfile';
import AboutMe from '../components/profile/AboutMe';
import IdealRole from '../components/profile/IdealRole';
import Employment from '../components/profile/Employment';
import IndustrySkills from '../components/profile/IndustrySkills';
import Education from '../components/profile/Education';
import Tickets from '../components/profile/Tickets';
import JobAds from '../components/job/JobAds';
import Ads from '../components/common/Ads';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: "#app",
    components: {
        LoadUser,
        UserProfile,
        AboutMe,
        IdealRole,
        Employment,
        IndustrySkills,
        Education,
        Tickets,
        JobAds,
        Ads,
        Alert,
        Navigation,
    },
});