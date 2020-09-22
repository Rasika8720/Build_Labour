<template>
    <div class="profile-item-2">
        <div class="profile-item-2">
            <div v-show="showJobs">
                <job-posts post-type="open_search"></job-posts>
            </div>
            <div v-show="showIndividuals">
                <company-people post-type="open_search" :user-id="`${userId}`"></company-people>
            </div>
            <div v-show="showCompanies">
                <company-preview post-type="open_search" :user-id="`${userId}`"></company-preview>
            </div>
        </div>
    </div>
</template>

<script>
    import JobPosts from '../job/JobPosts';
    import CompanyPeople from '../company/CompanyPeople';
    import CompanyPreview from '../company/CompanyPreview';

    export default {
        name: "search-results",
        data() {
            return {
                showIndividuals: false,
                showCompanies: false,
                showJobs: false,
                showDayLabour: false,
            }
        },
        props: {
            userId: {
                type: String,
                required: false
            }
        },
        methods: {

        },
        created() {

            let vm = this;

            Bus.$on('openSearchIndividuals', function(results) {
                vm.showIndividuals = true;
                vm.showCompanies = false;
                vm.showJobs = false;
                vm.showDayLabour = false;
            });

            Bus.$on('openSearchJobs', function(results) {
                vm.showIndividuals = false;
                vm.showCompanies = false;
                vm.showJobs = true;
                vm.showDayLabour = false;
            });

            // Bus.$on('openSearchDay_labour', function(results) {
            //     vm.showIndividuals = false;
            //     vm.showCompanies = false;
            //     vm.showJobs = false;
            //     vm.showDayLabour = true;
            // });

            Bus.$on('openSearchCompanies', function(results) {
                vm.showIndividuals = false;
                vm.showCompanies = true;
                vm.showJobs = false;
                vm.showDayLabour = false;
            });

            Bus.$emit('activateTab', 'search-jobs');
        },
        mounted() {

        },
        components: {
            JobPosts,
            CompanyPeople,
            CompanyPreview
        },
    }
</script>

<style scoped>

</style>
