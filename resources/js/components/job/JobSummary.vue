<template>
    <div class="profile-item-2">
        <delete-modal></delete-modal>
        <confirm-modal></confirm-modal>

        <ul class="list-job-items">
        <transition-group name="list">
            <li class="job-items" v-for="(post, index) in jobPosts" :key="index+0">
                <div class="profile-content">
                    <div class="row">
                        <div class="col-md-8 col-sm-8">
                            <div class="job-title mt-0">{{ post.template_name ? post.template_name : (post.title ? post.title : post.job_role.job_role_name) }}</div>
                            <div class="title-label">{{ post.template_name ? 'Template Name' : 'Job Title' }}
                            </div>
                            <div v-if="post.status != 1" class="text-danger mb-3" style="font-weight: bold; font-size: 14px;">
                                {{ post.status == 1 ? null : post.status == 2 ? 'Filled' : 'Closed'}}
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4 mb-3">
                            <div class="row ta-center">
                                <div class="col-icon icon-buttons" @click="onClickAction('preview', post, index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="31" height="22" viewBox="0 0 31 22">
                                        <g fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(1 1)">
                                            <path d="M.75 10s5.09-10 14-10 14 10 14 10-5.09 10-14 10-14-10-14-10z"/>
                                            <circle cx="14.75" cy="10" r="4"/>
                                        </g>
                                    </svg>
                                    <div class="icon-label">View Details</div>
                                </div>
                                <div class="col-icon icon-buttons" @click="onClickAction('edit', post, index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                                        <path fill="none" fill-rule="evenodd" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2a2.828 2.828 0 1 1 4 4L6.5 19.5 1 21l1.5-5.5L16 2z"/>
                                    </svg>
                                    <div class="icon-label">{{post.status ? 'Edit' : 'Post Again'}}</div>
                                </div>
                                <div class="col-icon icon-buttons" v-if="post.status" :style="! post.status ? 'cursor: default;' : ''" @click="onClickAction('duplicate', post, index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22">
                                        <g fill="none" fill-rule="evenodd" :stroke="post.status ? '#000' : '#d8d8d8'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" transform="translate(1 1)">
                                            <rect width="13" height="13" x="7" y="7" rx="2"/>
                                            <path d="M3 13H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>
                                        </g>
                                    </svg>
                                    <div class="icon-label" v-if="post.status" :style="! post.status ? 'color: #d8d8d8;' : ''">Duplicate</div>
                                </div>
                                <div class="col-icon icon-buttons" v-if="post.status" :style="! post.status ? 'cursor: default;' : ''" @click="onClickAction('delete', post, index)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22">
                                        <g fill="none" fill-rule="evenodd" stroke="#FF3939" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                            <path d="M1 5h18M17 5v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5m3 0V3a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M8 10v6M12 10v6"/>
                                        </g>
                                    </svg>
                                    <div class="icon-label delete" :style="! post.status ? 'color: #d8d8d8;' : ''">Close</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-0">
                    <div class="row">
                        <div class="col-md-5 col-sm-5">
                            <div class="row mb-4">
                                <div class="col-md-5 col-sm-5 job-label">Created</div>
                                <div class="col-md-7 col-sm-7 job-detail">{{ post.created_at_formatted }}</div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-5 col-sm-5 job-label">By</div>
                                <div class="col-md-7 col-sm-7 job-detail">{{ post.company.name }}</div>
                            </div>
                        </div>

                        <div class="col-md-7 col-sm-7">
                            <div class="row mb-4">
                                <div class="col-md-3 col-sm-3 job-label">Contract type</div>
                                <div class="col-md-9 col-sm-9 job-detail">{{ post.contract_type }}</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 col-sm-3 job-label">Location</div>
                                <div class="col-md-9 col-sm-9 job-detail">{{ post.location }}</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-3 col-sm-3 job-label">Salary</div>
                                <div class="col-md-9 col-sm-9 job-detail">${{ post.salary }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </transition-group>
        </ul>
    </div>
</template>

<script>
    import Api from '@/api';
    import DeleteModal from '../common/DeleteModal';
    import ConfirmModal from '../common/ConfirmModal';

    export default {
        name: "job-summary",
        data() {
            return {
                jobPosts: [],
                jobPostType: '',
                curPost: {},
                curIndex: -1,
                input: {
                    job_id: '',
                },
                endpoints: {
                    search: '/api/v1/job/search?keyword=',
                    delete: '/api/v1/job/',
                    duplicate: '/api/v1/job/',
                },
            }
        },
        props: {
            companyId: {
                type: String,
                required: false
            },
        },
        methods: {
            onClickAction(action, post, index) {
                this.curPost = post;
                this.curIndex = index;

                switch(action) {
                    case 'preview':
                        window.location.href = '/job/view/?cid=' + post.company_id + '&jid=' + post.id + '&v=details';
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
                        if (post.status) {
                            $('#deleteRecordModal').modal('show');
                            Bus.$emit('deleteJobPost', this.endpoints.delete + post.id + '/delete', post.company_id);
                        }
                        break;
                }
            },
            async duplicatePost() {
                let vm = this,
                    id = this.curPost.id,
                    cid = this.curPost.company_id,
                    isTemplate = this.curPost.is_template;

                await axios.post(this.endpoints.duplicate + id + (isTemplate ? '/duplicate-as-template' : '/duplicate'), {
                    confirmation: isTemplate ? 'duplicate_as_template' : 'duplicate',
                    company_id: cid,

                }, Utils.getBearerAuth()).then(function(response) {

                    vm.jobPosts.push(response.data.data.new_job);

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });
            },
            getJobPosts(endpoint) {
                let vm = this;

                Promise.resolve(Api.getJobPosts(endpoint)).then(function(data) {
                    vm.jobPosts = data.data.jobs;
                    console.log(vm.jobPosts);
                });
            },
        },
        created() {
            let vm = this;

            Bus.$on('searchJobPosts', function(keyword) {
                vm.getJobPosts(vm.endpoints.search + keyword + '&company_id=' + vm.companyId + '&status=' + vm.jobPostType);
            });

            Bus.$on('removeJobPost', function() {
                vm.jobPosts.splice(vm.curIndex, 1);
            });

            Bus.$on('duplicateJobPost', function() {
                vm.duplicatePost();
            });

            Bus.$on('getJobPosts', function(type, keyword) {
                vm.jobPosts = [];

                switch(type) {
                    case 'templates': vm.jobPostType = 'saved_templates'; break;
                    case 'active': vm.jobPostType = 'active_jobs'; break;
                    case 'closed': vm.jobPostType = 'past_jobs'; break;
                }

                vm.getJobPosts(vm.endpoints.search + keyword + '&company_id=' + vm.companyId + '&status=' + vm.jobPostType);
            });

            Bus.$emit('activateTab', 'jobs');
        },
        components: {
            DeleteModal,
            ConfirmModal,
        },
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
