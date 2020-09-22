<template>
    <transition name="slide-fade">
    <div class="profile-item-2" v-if="show">
        <div class="job-action">
            <div class="job-filter">
                <button :class="activeJobsCls" @click="onClickFilterTab('Active')">Active Jobs</button>
                <button :class="historyCls" @click="onClickFilterTab('History')" v-show="isDisplayHistory">History</button>
            </div>
            <!-- <div class="job-sort">
                Sort by: <span class="job-recent">Most Recent</span>
            </div> -->
        </div>
        <job-posts :company-id="companyId"></job-posts>
    </div>
    </transition>
</template>

<script>
    import JobPosts from '../job/JobPosts';

    export default {
        name: "company-jobs",
        data() {
            return {
                show: false,
                activeJobsCls: 'active',
                historyCls: '',
                isDisplayHistory: true
            }
        },
        props: {
            companyId: {
                type: String,
                required: false
            },
            viewer_type: {
                type: String,
                required: false
            },
        },
        created() {
            let vm = this;

            this.isDisplayHistory = this.viewer_type == 'viewer' ? false : true;

            Bus.$on('showCompanyJobs', function(flag) {
                vm.show = flag;
                
                Bus.$emit('getCompanyJobs', 'active_jobs');
                Bus.$emit('hideCompanyPeople');
                Bus.$emit('hideCompanyPosts');
            });

            Bus.$on('hideCompanyJobs', function() {
                vm.show = false;
            });
        },
        methods: {
            onClickFilterTab(tab) {
                this.activeJobsCls = tab == 'Active' ? 'active' : '';
                this.historyCls = tab == 'History' ? 'active' : '';

                Bus.$emit('getCompanyJobs', tab == 'Active' ? 'active_jobs' : 'past_jobs');
            },
            getInitials(name) {
                return Utils.getInitials(name);
            },
            getTimeDiffNow(created_at) {
                return Utils.formatTimeDiffNow(created_at);
            },
        },
        components: {
            JobPosts,
        },
    }
</script>

<style scoped>
    button {
        height: 36px;
        box-shadow: 0 0 0.5px 0 rgba(33, 49, 60, 0.08), 0 3px 19px 0 rgba(38, 52, 64, 0.09);
        background-color: #fff;
        color: #a2b2b7;
        margin-right: 20px;
        margin-top: 0px;
    }
    button.active {
        border: solid 2px #00aeef;
        background-color: #fff;
        color: #00aeef;
    }
    button:hover {
        border: solid 2px #00aeef;
        background-color: #f9f9f9;
    }
</style>