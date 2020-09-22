import Vue from 'vue';

import SearchResults from '../components/common/SearchResults';
import JobSearchFilters from '../components/job/JobSearchFilters';
import JobAds from '../components/job/JobAds';
import Ads from '../components/common/Ads';
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';
import CompanyMostRecentApplicants from "../../js/components/company/CompanyMostRecentApplicants";

new Vue({
    el: "#app",
    components: {
        SearchResults,
        JobSearchFilters,
        JobAds,
        CompanyMostRecentApplicants,
        Ads,
        Alert,
        Navigation,
    },
});