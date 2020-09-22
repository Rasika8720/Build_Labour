<template>
    <div>
        <delete-modal></delete-modal>
        <confirm-modal></confirm-modal>

        <div class="profile-item-2">
            <a href="javascript:void(0)" @click="goToJobs"><i class="fa fa-angle-left ml-2"></i> Back to Jobs</a>
        </div>

        <div class="profile-item-2" v-if="job && stats">
            <ul class="list-job-items">

                <li class="job-items">
                    <div class="profile-content">
                        <div class="row">
                            <div class="col-md-8 col-sm-8">
                                <div class="job-title mt-0"> {{ job.title ? job.title : job.job_role_name }}</div>
                                <div class="title-label mb-3">
                                    <label :class="{'text-success': job.status == 1, 'text-danger': !job.status || job.status == 2}">
                                        {{ job.status == 1 ? 'Active' : job.status == 2 ? 'Filled' : 'Inactive'}}
                                    </label>
                                    <small>{{ stats.no_of_views }} view</small>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 mb-3">
                                <div class="row ta-center">
                                    <div class="col-icon icon-buttons" @click="onClickAction('preview', job)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="31" height="22" viewBox="0 0 31 22">
                                            <g fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(1 1)">
                                                <path d="M.75 10s5.09-10 14-10 14 10 14 10-5.09 10-14 10-14-10-14-10z"/>
                                                <circle cx="14.75" cy="10" r="4"/>
                                            </g>
                                        </svg>
                                        <div class="icon-label">Preview</div>
                                    </div>
                                    <div class="col-icon icon-buttons" @click="onClickAction('edit', job)" v-if="is_my_company">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                                            <path fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2a2.828 2.828 0 1 1 4 4L6.5 19.5 1 21l1.5-5.5L16 2z"/>
                                        </svg>
                                        <div class="icon-label">{{job.status ? 'Edit' : 'Post Again'}}</div>
                                    </div>
                                    <div class="col-icon icon-buttons" :style="! job.status ? 'cursor: default;' : ''" @click="onClickAction('duplicate', job)" v-if="is_my_company && job.status">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                                            <g fill="none" fill-rule="evenodd" :stroke="job.status ? '#000' : '#d8d8d8'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(1 1)">
                                                <rect width="13" height="13" x="7" y="7" rx="2"/>
                                                <path d="M3 13H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                            </g>
                                        </svg>
                                        <div class="icon-label" :style="! job.status ? 'color: #d8d8d8;' : ''">Duplicate</div>
                                    </div>
                                    <div class="col-icon icon-buttons" @click="onClickAction('delete', job)" v-if="is_my_company && job.status">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22">
                                            <g fill="none" fill-rule="evenodd" stroke="#FF3939" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                                <path d="M1 5h18M17 5v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5m3 0V3a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M8 10v6M12 10v6"/>
                                            </g>
                                        </svg>
                                        <div class="icon-label delete">Close</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-0">
                        <div class="row">
                            <div class="col-md-5 col-sm-5">
                                <div class="row mb-4">
                                    <div class="col-md-5 col-sm-5 job-label">Created</div>
                                    <div class="col-md-7 col-sm-7 job-detail">{{ job.created_at_formatted }}</div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-md-5 col-sm-5 job-label">By</div>
                                    <div class="col-md-7 col-sm-7 job-detail">{{ job.company.name }}</div>
                                </div>
                            </div>

                            <div class="col-md-7 col-sm-7">
                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-3 job-label">Contract type</div>
                                    <div class="col-md-9 col-sm-9 job-detail">{{ job.contract_type }}</div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-3 job-label">Location</div>
                                    <div class="col-md-9 col-sm-9 job-detail">{{ job.location }}</div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-3 col-sm-3 job-label">Salary</div>
                                    <div class="col-md-9 col-sm-9 job-detail">${{ job.salary }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

    </div>
</template>

<script>
    import Api from '@/api';
    import DeleteModal from '../common/DeleteModal';
    import ConfirmModal from '../common/ConfirmModal';

    export default {
        name: "job-applicants-top",
        components: {
            DeleteModal,
            ConfirmModal,
        },
        props: {
            is_my_company: {
                type: Boolean,
                required: false
            }
        },
        data() {
            return {
                disabled: false,
                applicants: [],
                job: null,
                stats: null,
                sortBy: 'Most Recent',
                searchKeyword: '',
                endpoints: {
                    getJob: '/api/v1/company/',
                    getApplicants: '/api/v1/job/',
                    getStats: '/api/v1/job/',
                    duplicate: '/api/v1/job/',
                    goToJobList: '/job/list?type=active',
                    delete: '/api/v1/job/'
                },
            }
        },
        created() {

            let vm = this;
            let companyId = this.companyId ? this.companyId : Utils.getUrlParams().cid;

            this.endpoints.getJob = this.endpoints.getJob + companyId + '/jobs/' + Utils.getUrlParams().jid;
            this.endpoints.getApplicants = this.endpoints.getApplicants + Utils.getUrlParams().jid + '/applicants';
            this.endpoints.getStats = this.endpoints.getStats + Utils.getUrlParams().jid + '/stats';

            this.getJob();
            this.getStats();

            Bus.$on('duplicateJobPost', function() {
                vm.duplicatePost();
            });

            Bus.$on('removeJobPost', function() {
                window.location = vm.endpoints.goToJobList;
            });

        },
        methods: {

            onClickAction(action, post) {
                this.curPost = post;

                switch(action) {
                    case 'preview':
                        window.location.href = '/job/view/?cid=' + post.company_id + '&jid=' + post.id;
                        break;

                    case 'edit':
                        window.location.href = '/job/post?jid=' + post.id;
                        break;

                    case 'duplicate':
                        if (post.status) {
                            $('#confirmModal').modal('show');
                            Bus.$emit('confirmAction', 'duplicate');
                        }
                        break;

                    case 'delete':
                        $('#deleteRecordModal').modal('show');
                        Bus.$emit('deleteJobPost', this.endpoints.delete + post.id + '/delete', post.company_id);
                        break;
                }
            },


            goToJobs() {

                if (window.location.pathname != '/job/list?type=templates') {
                    window.location = '/job/list?type=templates';
                }
            },

            getJob() {

                let vm = this;
                axios.get(vm.endpoints.getJob, Utils.getBearerAuth())

                    .then(function(response) {

                        vm.job = response.data.data.job;
                    })
            },

            getStats() {

                let vm = this;
                axios.get(vm.endpoints.getStats, Utils.getBearerAuth())

                    .then(function(response) {

                        vm.stats = response.data.data.stats;
                        console.log(vm.stats);
                    })
            },

            async duplicatePost() {

                let vm = this,
                    id = this.job.id,
                    cid = this.job.company_id;

                await axios.post(this.endpoints.duplicate + id + '/duplicate', {
                    confirmation: 'duplicate',
                    company_id: cid,

                }, Utils.getBearerAuth()).then(function(response) {

                    window.location = vm.endpoints.goToJobList;

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });
            },
        }
    }
</script>

<style scoped>
    .job-title {
        font-size: 21px;
        font-weight: 600;
        color: #252829;
    }
    .title-label {
        font-size: 14px;
        font-weight: 500;
        color: #a2b2b7;
    }
    hr {
        opacity: 0.5;
        background-color: #f9f9f9;
    }
    .job-label {
        font-size: 15px;
        font-weight: 500;
        color: #000;
        display: inline;
    }
    .job-detail {
        font-size: 15px;
        letter-spacing: 0.22px;
        color: #6b7172;
        display: inline-block;
    }
    .icon-buttons:hover {
        cursor: pointer;
    }
    .icon-label {
        font-size: 10px;
        font-weight: 500;
        color: #000;
        margin-top: 2px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .icon-label.delete {
        color: #ff3939;
    }
    .col-icon {
        flex: 0 0 25%;
        max-width: 25%;
    }
</style>
