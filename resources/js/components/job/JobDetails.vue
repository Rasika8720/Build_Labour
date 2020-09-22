<template>
    <div class="profile-item-2">
        <div class="profile-content">
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h24v24H0z"/>
                        <path fill="#00aeef" d="M7 5V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4zm1.4 0h7.2v-.9a.7.7 0 0 0-.7-.7H9.1a.7.7 0 0 0-.7.7V5zM19 17h2c.818 0 1.544-.393 2-1v4a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-4c.456.607 1.182 1 2 1h2v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1h6v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1zm-1 0v1h-2v-1h2zM6 17h2v1H6v-1z"/>
                    </g>
                </svg>

                Job Details
                <span  v-if="status != 1" class="text-danger mb-3 float-right" style="font-weight: bold; font-size: 14px;">
                    {{ status == 2 ? 'Filled' : 'Closed'}}
                </span>
                <span  v-else-if="status == 1" class="text-success mb-3 float-right" style="font-weight: bold; font-size: 14px;">
                    Active
                </span>
            </div>

            <div class="job-header" v-if="title">
                {{ title }}
            </div>

            <div v-if="description">
                <div class="job-title">Job Description</div>
                <div class="job-body" v-html="description.replace(/(?:\r\n|\r|\n)/g, '<br />')">
                </div>
            </div>

            <div v-if="about">
                <div class="job-title">About the Project</div>
                <div class="job-body">
                    {{ about }}
                </div>
            </div>

            <div v-if="project_size">
                <div class="job-title">Project Size</div>
                <div class="job-body">
                    ${{ project_size }}
                </div>
            </div>

            <div v-if="exp_level">
                <div class="job-title">Experience Level</div>
                <div class="job-body">
                    {{ exp_level }}
                </div>
            </div>

            <div v-if="contract_type">
                <div class="job-title">Contract type</div>
                <div class="job-body">
                    {{ contract_type }}
                </div>
            </div>

            <div v-if="salary">
                <div class="job-title">Salary</div>
                <div class="job-body">
                    <span v-if="salary">$ {{ salary}}</span>
                    <span v-else>$ - - - , - - - </span>
                </div>
            </div>

            <div v-if="reports_to && reports_to.length > 0">
                <div class="job-title">Reports to</div>
                <div class="job-body">
                    <ul class="job-list-items-2">
                        <li v-for="(item, index) in reports_to" :key="index">
                            {{ item }}
                        </li>
                    </ul>
                </div>
            </div>

            <div v-if="location">
                <div class="job-title">Location</div>
                <div class="job-body">
                    {{ location }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "job-details",
        data() {
            return {
                show: true,
                reports_to_active_index: 0,
                reports_to_job_roles: [],
                job_roles: [],
                locations: [],
                job_role: '',
                title: '',
                description: '',
                about: '',
                project_size: '',
                exp_level: '',
                contract_type: '',
                salary: '',
                reports_to: [],
                location: '',
                status: '',
            }
        },
        created() {
            let vm = this;

            Bus.$on('jobDetails', function(details) {
                console.log(details);
                if (details) {
                    vm.title = details.title ? details.title : details.job_role.job_role_name;
                    vm.description = details.description;
                    vm.about = details.about;
                    vm.project_size = details.project_size;
                    vm.exp_level = details.exp_level;
                    vm.contract_type = details.contract_type;
                    vm.salary = details.salary;
                    vm.reports_to = details.reports_to;
                    vm.location = details.location;
                    vm.status = details.status;
                }
            });
        },
        methods: {

        }
    }
</script>
