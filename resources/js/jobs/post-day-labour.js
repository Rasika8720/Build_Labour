import Vue from 'vue';

import LoadJob from "../components/job/LoadJob";
import CreateJob from '../components/job/CreateJob';
import NewDayLabourDetails from "../components/job/create-job/day-labour/NewDayLabourDetails";
import NewDayLabourTrades from "../components/job/create-job/day-labour/NewDayLabourTrades";
import NewDayLabourRequirements from "../components/job/create-job/day-labour/NewDayLabourRequirements";
import Alert from '../components/common/Alert';
import Navigation from '../components/common/Navigation';

new Vue({
    el: '#app',
    components: {
        LoadJob,
        CreateJob,
        NewDayLabourDetails,
        NewDayLabourTrades,
        NewDayLabourRequirements,
        Alert,
        Navigation
    }
});
