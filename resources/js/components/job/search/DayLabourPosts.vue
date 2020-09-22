<template>
    <div>
        <div class="loading" style="position: unset; text-align: center;">
            <pulse-loader :loading="loading" color="#00aeef" size="10px"></pulse-loader>
        </div>
        <div v-show="noData && jobPosts.length == 0">
            No data found.
        </div>
        <ul class="list-job-items">
        <transition-group name="list-down">
            <li class="job-items" v-for="(post, index) in jobPosts" :key="index+0" v-if="post.company">
                <div class="profile-content" style="padding-bottom: 10px;">
                        <!-- <div class="save-icon">
                            <div class="star-cont">
                                <input class="star" type="checkbox" title="Bookmark Job" :ref="'savedJobPost-' + post.id"
                                    :value="post.id" v-model="checkedJobPosts" @click="save(post)" />
                            </div>

                            <div class="bl-label-14-style-2 bl-mt12">
                                Save
                            </div>
                        </div> -->

                    <a class="job-wrapper" :href="`/job/view?` + `cid=${post.company_id}&jid=${post.id}&v=details`" @click.prevent="open">
                        <div class="jobads-row">
                            <div class="bl-col-1">
                                <img v-if="post.company.photo_url" class="bl-image-40" :src="post.company.photo_url"
                                     @click="onClickCompanyPhoto(post.company_id)">

                                <avatar v-else cls="bl-image-40" size="40" border="0" border-radius="8px"
                                        :initials="getInitials(post.company.name)"
                                        :company-id="(post.company_id) ? post.company_id + '' : ''">
                                </avatar>
                            </div>
                            <div class="bl-col-2">

                                    <div class="bl-display">
                                        <span class="bl-label-19 bl-ml14">
                                            {{ post.company.name }}
                                        </span>

                                            <span class="bl-label-14 bl-ml14" style="margin-top:-5px">
                                            {{ getTimeDiffNow(post.created_at) }}
                                        </span>
                                    </div>
                            </div>
                        </div>

                        <div class="job-summary">
                            <div class="bl-label-21">
                                {{ post.title ? post.title : post.job_role_name }}
                            </div>
                            <div class="bl-label-14-style-3">
                                {{ post.location }}
                            </div>
                            <div class="bl-label-15 bl-mt16">
                                {{ post.description }}
                            </div>
                        </div>
                    </a>
                    <div class="row mt-3 stats-wrapper">
                        <div class="col-md-2 col-sm-2">
                            <div class="applicant-no">{{ post.stat_total }}</div>
                            <div class="applicant-label">Total</div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <div class="applicant-no">{{ post.stat_new }}</div>
                            <div class="applicant-label">New</div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <div class="applicant-no">{{ post.stat_invited }}</div>
                            <div class="applicant-label">Invited</div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <div class="applicant-no">{{ post.stat_favourite }}</div>
                            <div class="applicant-label bl-ellipsis">Suitable</div>
                        </div>
                        <div class="col-md-2 col-sm-2">
                            <div class="applicant-no" style="color: #969696;">{{ post.stat_not_suitable }}</div>
                            <div class="applicant-label bl-ellipsis">Not Suitable</div>
                        </div>
                    </div>

                    <div class="profile-more mt-2">
                        <a :href="'/job/view/?cid=' + post.company_id + '&jid=' + post.id + '&v=details'">
                            <!--<a :href="'/job/applicants/?cid=' + post.company_id + '&jid=' + post.id">-->
                            View Details<i class="fa fa-angle-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </li>
        </transition-group>
        </ul>
    </div>
</template>

<script>
    import Api from '@/api';
    import Avatar from '../../common/Avatar';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "day-labour-posts",
        data() {
            return {
                loading: false,
                noData: false,
                jobPosts: [],
                checkedJobPosts: [],
                input: {
                    job_id: '',
                },
                endpoints: {
                    get: '/api/v1/company/',
                    search: '/api/v1/job/search?keyword=',
                    save: '/api/v1/bookmarks',
                    delete: '/api/v1/job/'
                },
            }
        },
        props: {
            companyId: {
                type: String,
                required: false
            },
            postType: {
                type: String,
                default: 'search'
            },
        },
        computed: {
            endpointGet() {
                return this.endpoints.get + this.companyId + '/posts/jobs';
            }
        },
        created() {
            let vm = this;

            if (this.postType == 'open_search') {
                Bus.$on('openSearchJobs', function(results) {
                    vm.jobPosts = [];
                    vm.noData = false;
                    vm.loading = true;

                    setTimeout(function() {
                        vm.jobPosts = results;
                        vm.loading = false;
                        if (vm.jobPosts.length == 0) vm.noData = true;
                    }, 1000);
                });
            }

            if (this.postType == 'search') {
                Bus.$on('searchJobPosts', function(keyword, location) {
                    vm.jobPosts = [];
                    vm.loading = true;

                    setTimeout(function() {
                        vm.getJobPosts(vm.endpoints.search + keyword + '&location=' + location);
                        vm.loading = false;
                    }, 1000);
                });

                Bus.$on('getCompanyJobs', function(status) {
                    vm.getJobPosts(vm.endpoints.search + '&status=' + status);
                });

                this.getJobPosts(vm.endpoints.search + '&status=active');
            }
        },
        methods: {
            open(event) {

            },
            getInitials(name) {
                return Utils.getInitials(name);
            },
            getJobPosts(endpoint) {
                let vm = this;

                let temp_endpoint = vm.companyId ? endpoint + '&company_id=' + vm.companyId : endpoint;

                Promise.resolve(Api.getJobPosts(temp_endpoint)).then(function(data) {
                    vm.jobPosts = data.data.jobs;
                });
            },
            getTimeDiffNow(created_at) {
                return Utils.formatTimeDiffNow(created_at);
            },
            onClickCompanyPhoto(company_id) {
                Api.redirectToCompanyProfile(company_id);
            },
            async save(post) {
                let vm = this;

                this.input.job_id = post.id;

                await axios.post(vm.endpoints.save, vm.$data.input, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    Bus.$emit('saveJobPost', post, vm.$refs['savedJobPost-' + post.id][0].checked);

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },
        },
        components: {
            Avatar,
            PulseLoader,
        },
    }
</script>

<style scoped>

</style>
