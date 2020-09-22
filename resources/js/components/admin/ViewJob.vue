<template>
    <div class="form-group" v-if="show">
        <div class="row">
            <div class="col-md-6">
                <div class="record-title">
                    {{ record.title }}
                </div>
                <div class="row" v-if="record.id != 0">
                    <div class="col-md-12">
                        <label class="record-label">ID</label>

                        <input type="text" class="form-control record-input" v-model="record.id" disabled />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">TITLE</label>

                        <input type="text" class="form-control record-input" v-model="record.title"  @keyup="onSearchJob(record.title)"/>

                        <span class="err-msg bl-ml-20" v-if="errors.title">
                            {{ errors.title }}
                        </span>
                    </div>

                </div>
                <div class="row" v-show="show_job_roles">
                    <div class="col-md-4">
                        <div class="form-group" style="margin-top:0" v-if="record.title && job_roles && job_roles.length > 0">
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                                    @click="onSelectJob(job)">

                                    {{ job.job_role_name }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">DESCRIPTION</label>
                    </div>

                    <div class="col-md-12">
                        <textarea class="record-textarea" style="height:180px;border:1px solid #ced4da;" v-model="record.description"></textarea>
                    </div>

                    <span class="err-msg bl-ml-30" style="margin-top:-6px;" v-if="errors.description">
                        {{ errors.description }}
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">ABOUT</label>
                    </div>

                    <div class="col-md-12">
                        <textarea class="record-textarea" style="height:180px;border:1px solid #ced4da;" v-model="record.about"></textarea>
                    </div>

                    <span class="err-msg bl-ml-30" style="margin-top:-6px;" v-if="errors.about">
                        {{ errors.about }}
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">EXPERIENCE LEVEL</label>

                        <input type="text" class="form-control record-input" v-model="record.exp_level" />

                        <span class="err-msg bl-ml-20" v-if="errors.exp_level">
                            {{ errors.exp_level }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">CONTRACT TYPE</label>

                        <input type="text" class="form-control record-input" v-model="record.contract_type" />

                        <span class="err-msg bl-ml-20" v-if="errors.contract_type">
                            {{ errors.contract_type }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">SALARY</label>

                        <input type="text" class="form-control record-input" v-model="record.salary" />

                        <span class="err-msg bl-ml-20" v-if="errors.salary">
                            {{ errors.salary }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">REPORTS TO</label>

                        <input type="text" class="form-control record-input" v-model="record.reports_to_str" />

                        <span class="err-msg bl-ml-20" v-if="errors.reports_to">
                            {{ errors.reports_to }}
                        </span>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label class="record-label">LOCATION</label>

                        <input type="text" class="form-control record-input" v-model="record.location" />

                        <span class="err-msg bl-ml-20" v-if="errors.location">
                            {{ errors.location }}
                        </span>
                    </div>
                </div>

                <div class="form-group row mt-5">
                    <div class="col-md-6">
                        <div class="btn btn-link btn-delete mb-3" style="margin-right:180px" data-dismiss="modal" @click="deleteRecord">
                            Delete
                        </div>
                        <button type="submit" @click="submit" :disabled="disabled">
                            Save Changes
                        </button>

                    </div>

                </div>
            </div>
            <div class="col-md-6">
                <div class="record-title">
                    Job Applicants
                </div>
                <div class="applicant-wrapper">
                    <span v-if="applicants.length === 0">
                        No Applicants to show
                    </span>
                    <span v-else>
                        <div class="user" v-for="(applicant, index) in applicants" :key="index">
                            <h5>{{applicant.full_name}}</h5>
                            <p>
                                <a :href="'/user/profile/' + applicant.id" target="_blank">View Profile</a>
                            </p>
                        </div>
                    </span>
                </div>
            </div>
        </div>
        <DeleteModal></DeleteModal>
    </div>
</template>

<script>
    import DeleteModal from '../common/DeleteModal';
    import Api from '@/api';

    export default {
        name: "view-job",
		data() {
			return {
                show_job_roles:false,
                disabled: false,
                show: false,
				record: {
                    id: 0, title: '', description: '', about: '', exp_level: '',
                    contract_type: '', salary: '', reports_to: [], reports_to_str: '', location: '',job_role_id:'',
                },
                applicants: [],
                errors: {
                    title: '', description: '', about: '', exp_level: '',
                    contract_type: '', salary: '', reports_to: '', location: '',
                },
                job_roles: [],
				endpoints: {
                    get: '',
                    save: '',
                    job_roles: '/api/v1/admin/job/roles',
                    delete: '/api/v1/admin/job/',
				}
			}
		},

		created() {
			let vm = this;

            Bus.$on('datatableViewJob', function(id){
                vm.show = true;

                if (id != 0) {
                    vm.endpoints.get = '/api/v1/admin/job/get?id=' + id;
                    vm.endpoints.save = '/api/v1/job/' + id;

                    vm.viewRecord();
                    vm.getApplicants(id);

                } else {
                    Utils.setObjectValues(vm.record, '');

                    vm.endpoints.save = '/api/v1/job';
                }
            });

            axios.get(vm.endpoints.job_roles, Utils.getBearerAuth())

            .then(function(response) {

                vm.job_roles = response.data.data.job_roles;

            }).catch(function(error) {

                Utils.handleError(error);
            });

            Bus.$on('removeJob', function() {
                Bus.$emit('adminSaveChanges', vm.record.id);
            });

		},

		methods: {

		  	viewRecord() {
				let vm = this;

				axios.get(vm.endpoints.get, Utils.getBearerAuth())

                .then(function(response) {
                    vm.record = response.data.data.record;

                    if (vm.record.job_role) {
                        vm.record.title = vm.record.job_role.job_role_name;
                    }

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },

            onChange(event) {

                let findById = parseInt(event.target.value);

                let findRole = this.job_roles.find(x => x.id === findById);

                if (findRole) {

                    this.record.title = findRole.job_role_name;
                }
            },

            async submit() {
                let vm = this;

                Utils.setObjectValues(this.errors, '');

                this.disabled = true;

                let results = this.record.reports_to_str.split(',');

                this.record.reports_to = [];

                for (let i = 0; i < results.length; i++) {
                    this.record.reports_to.push(results[i].trim());
                }

                await axios.post(vm.endpoints.save, vm.$data.record, Utils.getBearerAuth())

                .then(function(response) {

                    Bus.$emit('adminSaveChanges', vm.record.id);

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                this.disabled = false;
            },

            async deleteRecord () {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteJob', this.endpoints.delete + this.record.id);
            },
            onSearchJob(keyword) {
                this.show_job_roles=true;
                this.record.job_role_id = '';

                this.job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];
            },
            onSelectJob(job) {
                this.record.job_role_id = job.id;
                this.record.title = job.job_role_name;

                this.job_roles = [];
            },
            getApplicants(id)
            {
                console.log('retrieving applicants');
                let vm = this,
                    endpoint = '/api/v1/job/' + id + '/applicants';

                axios.get(endpoint, Utils.getBearerAuth()).then(function(response) {

                    console.log(response);
                    vm.applicants = response.data.data.applicants;

                }).catch(function(error) {

                    console.log(error);
                });
            }
        },
        components: {
            DeleteModal,
        },
	}
</script>
