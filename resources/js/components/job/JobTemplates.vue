<template>
    <div class="profile-item-2">
        <div class="profile-content">
            
            <div class="bl-label-20-style-2">
                Create Job
            </div>

            <form>
                <div class="emp-row">
                    <div class="template-title mb-2">Create a New position</div>

                    <div class="template-col-left">
                        <input type="text" class="form-control" style="padding-left:24px"
                            placeholder="Name of Template..."
                            v-model="input.title"
                            @keyup="onSearchJob(input.title)">
                    </div>
                    <div class="template-col-right">
                        <button class="add-button template-add-btn" type="button">&#43;</button>
                    </div>
                </div>

                <div class="form-group" style="margin-top:0" v-if="input.title && job_roles.length > 0">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                            @click="onSelectJob(job)">
                            
                            {{ job.job_role_name }}
                        </li>
                    </ul>
                </div>

                <div class="form-group">
                    <div class="template-title mb-2">Existing Templates</div>
                    <ul class="template-list">
                        <li>
                            <div class="template-list-item">
                                Project Manager
                            </div>
                        </li>
                        <li v-for="n in 10" :key="n">
                            Example {{ n }}
                        </li>
                    </ul>
                </div>

                <button style="width:100%" :disabled="disabled" @click="useTemplate()">
                    Use Selected
                </button>
            </form>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "job-templates",
        data() {
            return {
                disabled: false,
                show: true,
                reports_to_active_index: 0,
                reports_to_job_roles: [],
                job_roles: [],
                locations: [],
                job_role: '',
                title: '',
                description: '',
                about: '',
                exp_level: '',
                contract_type: '',
                salary: '',
                reports_to: [],
                location: '',
                input: {
                    job_role_id: '', title: '', description: '', about: '', exp_level: '',
                    contract_type: '', salary: '', reports_to: [], location: '',
                },
                errors: {
                    title: '', description: '', about: '', exp_level: '',
                    contract_type: '', salary: '', reports_to: '', location: '',
                },
                endpoints: {
                    post: '/api/v1/job',
                    save: '/api/v1/job/save-template',
                },
            }
        },
        created() {
            let vm = this;

            Bus.$on('jobDetails', function(details) {
                if (details) {
                    vm.title = details.title ? details.title : details.job_role.job_role_name;
                    vm.description = details.description;
                    vm.about = details.about;
                    vm.exp_level = details.exp_level;
                    vm.contract_type = details.contract_type;
                    vm.salary = details.salary;
                    vm.reports_to = details.reports_to;
                    vm.location = details.location;
                
                } else {
                    vm.show = false;
                }
            });

            Bus.$on('postJob', function(isTemplate) {
                if (isTemplate) {
                    vm.submit(vm.endpoints.save)
                
                } else {
                    vm.submit(vm.endpoints.post);
                }
            });

            this.input.reports_to.push('');
        },
        methods: {
            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },
            onChangeLocation(keyword) {
                this.locations = (keyword && keyword.length > 0) ? Api.getLocations(keyword) : [];
            },
            onSelectLocation(location) {
                this.input.location = location;
                
                this.locations = [];
            },
            onSearchJob(keyword) {
                this.input.job_role_id = '';

                this.job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];
            },
            onSearchReportsTo(keyword, index) { 
                this.reports_to_job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];

                this.reports_to_active_index = index;
            },
            onSelectReportsTo(job) {
                this.input.reports_to[this.reports_to_active_index] = job.job_role_name;

                this.reports_to_job_roles = [];
            },
            onSelectJob(job) {
                this.input.job_role_id = job.id;
                this.input.title = job.job_role_name;

                this.job_roles = [];
            },
            addNewEntity() {
                this.input.reports_to = this.input.reports_to.filter(r => r !== '');

                this.input.reports_to.push('');
            },
            removeEntity(index) {
                if (this.input.reports_to.length > 1) {
                    this.input.reports_to.splice(index, 1);
                }
            },
            async submit(endpoint) {
                let vm = this;

                Utils.setObjectValues(this.errors, '');
                
                await axios.post(endpoint, vm.$data.input, Utils.getBearerAuth())
                    
                .then(function(response) {
                    let data = response.data,
                        job = data.data.job;
                    
                    if (job.is_template) {
                        Bus.$emit('alertSuccess', data.message);
                        
                        Utils.setObjectValues(vm.input, '');

                    } else {
                        window.location.href = '/job/view?cid=' + job.company_id + '&jid=' + job.id;
                    }
                
                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);
        
                    if (inputErrors) vm.errors = inputErrors;
                });

                Bus.$emit('postedJob');
            },
        }
    }
</script>