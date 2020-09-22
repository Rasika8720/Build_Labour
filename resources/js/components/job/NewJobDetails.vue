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
            </div>

            <form>

                <div class="form-group">
                    <div class="job-title mb-2">Job Title</div>

                    <input type="text" class="form-control" style="margin: 0; max-width: 524px;" placeholder="Start typing"
                           v-model="input.title"
                           @keyup="onSearchJob(input.title)"
                           @focus="onFocus('job_title')"
                           @blur="onLeave()"/>

                    <span class="err-msg" v-if="errors.title">
                        {{ errors.title }}
                    </span>
                </div>

                <div class="form-group" style="margin-top:0" v-if="input.title && job_roles && job_roles.length > 0">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                            @click="onSelectJob(job)">

                            {{ job.job_role_name }}
                        </li>
                    </ul>
                </div>

                <div class="form-group">
                    <div class="job-title">Job Description</div>

                    <textarea rows="3" ref="jobDesc" class="form-control" style="overflow: hidden;"
                              placeholder="Example: The Project Manager is accountable for the leadership and management of their nominated project including the achievement of safety, quality, commercial and programme objectives and the effective day to day management of the project team."
                              @focus="textAreaAdjust('jobDesc')"
                              @keyup="textAreaAdjust('jobDesc')"
                              v-model="input.description">
                    </textarea>

                    <span class="err-msg" v-if="errors.description">
                        {{ errors.description }}
                    </span>
                </div>

                <div class="form-group">
                    <div class="job-title mb-2">Contract Type</div>

                    <div class="me-row">
                        <select v-model="input.contract_type">
                            <option key="1" value="Full-time Permanent">Full-time Permanent</option>
                            <option key="2" value="Full-time Fixed term">Full-time Fixed term</option>
                            <option key="3" value="Part-Time">Part-Time</option>
                            <option key="4" value="Casual">Casual</option>
                            <option key="5" value="Sub Contractor">Sub Contractor (ABN required)</option>
                            <option key="6" value="Hourly Rate">Hourly Rate</option>
                        </select>

                        <span class="err-msg" v-if="errors.contract_type">
                            {{ errors.contract_type }}
                        </span>
                    </div>
                </div>

                <!--<div class="form-group">-->
                <!--<div class="job-title mb-2">Salary</div>-->

                <!--<input type="text" class="form-control" placeholder="$ Enter amount"-->
                <!--pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"-->
                <!--data-type="currency"-->
                <!--v-model="input.salary"-->
                <!--@keyup="formatCurrency('salary', $event)"-->
                <!--@blur="formatCurrency('salary', $event, 'blur')">-->

                <!--<span class="err-msg" v-if="errors.salary">-->
                <!--{{ errors.salary }}-->
                <!--</span>-->
                <!--</div>-->


                <div class="form-group">

                    <div class="job-title mb-2">Salary</div>

                    <div class="input-toggle">

                        <input type="text" class="form-control" placeholder="$ Enter amount"
                               pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                               data-type="currency"
                               v-model="input.salary"
                               @keyup="formatCurrency('salary', $event)"
                               @blur="formatCurrency('salary', $event, 'blur')">
                    </div>
                </div>

                <div class="form-group">
                    <div class="job-title mb-2">Location (suburb/town)</div>

                    <input type="text" class="form-control" placeholder="Start typing address..."
                           v-model="input.location"
                           @focus="onFocus('locations')"
                           @keyup="onChangeLocation(input.location)"
                           @blur="onLeave()"
                    >

                    <span class="err-msg" v-if="errors.location">
                        {{ errors.location }}
                    </span>
                </div>

                <div class="emp-row" style="margin-top:0" v-if="locations && locations.length > 0 && focusTo == 'locations'">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(place, idx) in locations" :key="idx"
                            @click="onSelectLocation(place.place_name)">
                            {{ place.place_name }}
                        </li>
                    </ul>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "new-job-details",
        data() {
            return {
                reports_to_active_index: 0,
                reports_to_job_roles: [],
                exp_levels: [],
                job_roles: [],
                locations: [],
                input: {
                    id: '', is_template: '', status: '', template_name: '',
                    job_role_id: '', title: '', description: '',
                    contract_type: '', salary: '', salary_type: 'Salary', location: '',
                },
                errors: {
                    title: '', description: '',
                    contract_type: '', salary: '', location: '',
                },
                salaryType: 'Salary',
                salaryPlaceholder: 'Salary per annum',
                leaveTimeoutHandler: null,
                timeoutHandler: null,
                focusTo: null
            }
        },
        created() {
            let vm = this;

            Bus.$emit('activateTab', 'jobs');

            Bus.$on('saveJob', function() {
                Bus.$emit('newJobDetails', vm.input);
            });

            Bus.$on('newJobDetailsError', function(errors) {
                console.log(errors);
                vm.errors = errors;
            });

            Bus.$on('jobDetails', function(details) {

                if (details) {

                    vm.input.id = details.id;
                    vm.input.is_template = details.is_template;
                    vm.input.status = details.status;
                    vm.input.template_name = details.template_name;
                    vm.input.title = details.title ? details.title : details.job_role.job_role_name;
                    vm.input.description = details.description;
                    vm.input.contract_type = details.contract_type;
                    vm.input.salary = details.salary;
                    vm.input.salary_type = details.salary_type ? details.salary_type : details.salary_type;
                    vm.input.location = details.location;

                    let vm2 = vm;

                    Bus.$emit('editJobPost', vm2.input.template_name, vm2.input.status, vm2.input.is_template);
                }
            });

            Bus.$on('clearNewJobDetails', () => {

                this.reports_to_job_roles = [];
                this.locations = [];
            });

            this.input.reports_to.push('');
        },
        methods: {
            formatCurrency(field, e, blur = null) {
                this.input[field] = Utils.formatCurrency(e, blur);
            },
            formatCheckbox(refName, value) {
                Utils.formatCheckbox(this.$refs, this.input, refName, value);
            },
            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },
            onFocus(type) {

                this.focusTo = type;

                this.reports_to_job_roles = [];
                this.locations = [];

                Bus.$emit('clearNewJobRequirements');
                Bus.$emit('clearNewJobResponsibilities');
            },
            onLeave(type) {

                if (this.leaveTimeoutHandler) {

                    clearTimeout(this.leaveTimeoutHandler)
                }

                this.leaveTimeoutHandler = setTimeout(() => {

                    this.reports_to_job_roles = [];
                    this.locations = [];
                    this.job_roles = [];

                }, 2000)
            },
            async onChangeLocation(keyword) {

                let vm = this;

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler);
                }

                if (keyword && keyword.length > 0) {

                    this.timeoutHandler = await setTimeout(() => {

                        Promise.resolve(Api.getLocationsPromise(keyword)).then((data) => {
                            this.locations = (data.data && data.data.locations) ? data.data.locations.features : [];
                        });
                    }, 400);

                } else {

                    this.locations = [];
                }
            },
            onSelectSalaryType(type) {

                this.input.salary_type = type;

                this.salaryPlaceholder = this.input.salary_type === 'Salary' ? 'Salary per annum' : 'Wage per hour';

            },
            onSelectLocation(location) {
                this.input.location = location;

                this.locations = [];
            },
            onSearchJob(keyword) {

                let vm = this;

                this.input.job_role_id = '';

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler);
                }

                if (keyword && keyword.length > 0) {

                    this.timeoutHandler = setTimeout(() => {

                        Api.searchJobRoles(keyword).then((result) => {

                            vm.job_roles = result.data.job_roles
                        });

                    }, 300)
                }

            },
            async onSearchReportsTo(keyword, index) {

                let vm = this;

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler);
                }

                if (keyword && keyword.length > 0) {

                    this.timeoutHandler = await setTimeout(() => {

                        Api.searchJobRoles(keyword).then((result) => {

                            vm.reports_to_job_roles = result.data.job_roles
                        });

                    }, 300)
                }

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
            onSelectExpLevel(levelName) {

                this.input.exp_level = levelName;

                this.exp_levels = [];
            },
            onSearchExpLevels(keyword) {

                if (keyword && keyword.length > 0) {

                    let expLevels = [
                        {id: 1, name: "Entry Level"},
                        {id: 2, name: "Intermediate"},
                        {id: 3, name: "Junior"},
                        {id: 4, name: "Senior"}
                    ];

                    this.exp_levels = expLevels;

                    return this.exp_levels;
                }

                this.exp_levels = [];

                return [];
            },
            addNewEntity() {

                this.reports_to_job_roles = [];

                this.input.reports_to = this.input.reports_to.filter(r => r !== '');

                this.input.reports_to.push('');
            },
            removeEntity(index) {

                this.reports_to_job_roles = [];

                if (this.input.reports_to.length > 1) {
                    this.input.reports_to.splice(index, 1);
                }

                if (this.input.reports_to.length <= 1) {
                    this.input.reports_to = [];
                    this.input.reports_to.push('');
                }
            },
        }
    }
</script>
