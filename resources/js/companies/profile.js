import Vue from 'vue';

import LoadCompany from '../components/company/LoadCompany';
import CompanyProfile from '../components/company/CompanyProfile';
import CompanyBrowse from '../components/company/CompanyBrowse';
import CompanyPosts from '../components/company/CompanyPosts';
import CompanyPeople from '../components/company/CompanyPeople';
import CompanyJobs from '../components/company/CompanyJobs';
import CompanyMostRecentApplicants from '../components/company/CompanyMostRecentApplicants';
import Ads from '../components/common/Ads';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: "#app",
    components: {
        LoadCompany,
        CompanyProfile,
        CompanyBrowse,
        CompanyPosts,
        CompanyPeople,
        CompanyJobs,
        CompanyMostRecentApplicants,
        Ads,
        Alert,
        Navigation,
    },
});
