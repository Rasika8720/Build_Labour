<template>
    <div class="profile-item-2">

        <ul class="list-job-items">
        <transition-group name="list">
            <li class="job-items" v-for="(post, index) in jobPosts" :key="index+0">
                <div class="profile-content">
                    <div class="row">
                        <div class="col-md-1">
                            <img :src="post.company_image" style="width: 100%;">
                        </div>
                        <div class="col-md-6 col-sm-6">
                            <div class="job-title mt-0">{{ post.template_name ? post.template_name : (post.title ? post.title : post.job_role.job_role_name) }}</div>
                            <div class="title-label">{{ post.template_name ? 'Template Name' : 'Job Title' }}
                            </div>
                            <div  v-if="post.status != 1" class="text-danger mb-3" style="font-weight: bold; font-size: 14px;">
                                {{ post.status == 2 ? 'Filled' : 'Closed'}}
                            </div>
                            <div  v-else-if="post.status == 1" class="text-success mb-3" style="font-weight: bold; font-size: 14px;">
                                Active
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
                                    <div class="icon-label">View Listing</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mt-0">
                    <div class="row">
                        <div class="col-md-5 col-sm-5">
                            <div class="row mb-4">
                                <div class="col-md-5 col-sm-5 job-label">Applied</div>
                                <div class="col-md-7 col-sm-7 job-detail">{{ post.formatted_date }}</div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-5 col-sm-5 job-label">By</div>
                                <div class="col-md-7 col-sm-7 job-detail">{{ post.company_name }}</div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-5 col-sm-5 job-label">Your Application</div>
                                <div class="col-md-7 col-sm-7 job-detail" style="font-weight: bold;">
                                    <span :class="post.selected == 'Accepted' ? 'text-success' : post.selected ==  'Pending' ? 'text-orange' :'text-danger'">
                                        {{ post.selected }}
                                    </span>
                                </div>
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

    export default {
        name: "user-job-summary",
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
                },

            }
        },
        methods: {
            onClickAction(action, post, index) {
                this.curPost = post;
                this.curIndex = index;

                switch(action) {
                    case 'preview':
                        window.location.href = '/job/view/?cid=' + post.company_id + '&jid=' + post.job_id + '&v=details';
                        break;
                }
            },

            getJobPosts() {
                let vm = this,
                    endpoint = '/api/v1/job/applied-search';

                Promise.resolve(Api._get(endpoint)).then(function(data) {
                    vm.jobPosts = data.data;
                    console.log(vm.jobPosts);
                });
            },
        },
        created() {
            let vm = this;

            this.getJobPosts();
            Bus.$emit('activateTab', 'jobs');
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
    .text-orange {
        color: #ffc254;
    }
</style>
