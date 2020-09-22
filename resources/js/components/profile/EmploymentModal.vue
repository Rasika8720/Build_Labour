<template>
    <main-modal id="modalEmployment">

        <template slot="custom-modal-title">
            <h4 class="modal-title">{{ current == -1 ? 'Add' : 'Edit' }} Employment History</h4>
            <div class="close" data-dismiss="modal" @click="close">&times;</div>
        </template>

        <template slot="custom-modal-content">

            <form class="modal-form" method="POST" @submit.prevent="submit">
                <div class="emp-label">Job Details</div>

                <alert-employment v-if="isDisplayAlert"></alert-employment>

                <div class="form-group">
                    <div class="emp-row">
                        <div class="modal-form-label">Your Role</div>

                        <input class="form-control" type="text" v-model="job_role"
                            @focus="hasFocusRole(true)"
                            @keyup="onSearchJob(job_role)"
                            @blur="onLeave('job_role')"
                        />

                        <span class="err-msg" v-if="errors.job_role">
                            {{ errors.job_role }}
                        </span>
                    </div>

                    <div class="emp-row" style="margin-top:0" v-if="has_focus_role && job_roles && job_roles.length > 0">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                                @click="onSelectJob(job)">

                                {{ job.job_role_name }}
                            </li>
                        </ul>
                    </div>

                    <div class="emp-row">
                        <div class="modal-form-label">Company/Project Name</div>

                        <input class="form-control" type="text" v-model="company_name"
                            @focus="hasFocusCompany(true)"
                            @keyup="onSearchCompany(company_name)"/>

                        <span class="err-msg" v-if="errors.company_name">
                            {{ errors.company_name }}
                        </span>
                    </div>

                    <div class="emp-row" style="margin-top:0" v-if="has_focus_company && companies && companies.length > 0">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="(company, idx) in companies" :key="idx"
                                @click="onSelectCompany(company)">
                                {{ company.name }}
                            </li>
                        </ul>
                    </div>

                    <div class="emp-row">
                        <div class="modal-form-label">Location</div>

                        <input class="form-control" type="text" v-model="location"
                            ref="locationRef"
                            @focus="hasFocusLocation(true)"
                            @keyup="onChangeLocation(location)" />

                        <span class="err-msg" v-if="errors.location">
                            {{ errors.location }}
                        </span>
                    </div>

                    <div class="emp-row" style="margin-top:0" v-if="has_focus_location && locations && locations.length > 0">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="(place, idx) in locations" :key="idx"
                                @click="onSelectLocation(place.place_name)">
                                {{ place.place_name }}
                            </li>
                        </ul>
                    </div>

                    <div class="emp-row">
                        <div class="modal-form-label">Size of the Project (optional) <small style="color:gray">(e.g estimated value of $100,000)</small></div>

                        <input class="form-control" type="text" v-model="project_size"
                               pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                               data-type="currency"
                               @keyup="formatCurrency('project_size', $event)"
                            @focus="hasFocus()" />

                        <span class="err-msg" v-if="errors.project_size">
                            {{ errors.project_size }}
                        </span>
                    </div>
                </div>

                <div class="emp-label">Duration of Employment</div>
                <div class="emp-row" style="margin-top:36px">
                    <div class="role-col-left">
                        <div class="emp-form-label">Start Year</div>
                        <select v-model="start_year" @focus="hasFocus()">
                            <option v-for="(year, index) in years" :key="index" v-bind:value="year">{{ year }}</option>
                        </select>
                        <span class="err-msg" v-if="errors.start_year">
                            {{ errors.start_year }}
                        </span>
                    </div>
                    <div class="role-col-right" v-if="!isCurrent">
                        <div class="emp-form-label">End Year</div>
                        <select v-model="end_year" @focus="hasFocus()">
                            <option v-for="(year, index) in years" :key="index" v-bind:value="year">{{ year }}</option>
                        </select>
                        <span class="err-msg" v-if="errors.end_year">
                            {{ errors.end_year }}
                        </span>
                    </div>
                </div>

                <div class="emp-row" style="margin-top:36px">
                    <div class="role-col-left">
                        <div class="emp-form-label">Duration Number</div>
                        <select v-model="duration_number" @focus="hasFocus()">
                            <option v-for="(number, index) in numberRange" :key="index" v-bind:value="number">{{ number }}</option>
                        </select>
                    </div>
                    <div class="role-col-right">
                        <div class="emp-form-label">Duration Type</div>
                        <select v-model="duration_type" @focus="hasFocus()">
                            <option value="weeks">Weeks</option>
                            <option value="months">Months</option>
                            <option value="years">Years</option>
                        </select>
                    </div>
                </div>

                <div class="emp-row">
                    <div class="emp-label" style="margin-top:0">
                        <input class="styled-checkbox-2" id="styled-checkbox-current-2" type="checkbox"
                            ref="styled-checkbox-2"
                            v-model="isCurrent"
                            @focus="hasFocus()"
                            @change="onChangeCurrentRole" />
                        <label for="styled-checkbox-current-2">Currently in this Role</label>
                    </div>
                </div>

                <div class="emp-label" style="margin-bottom:17px">Responsibilities (list major only)</div>

                <textarea class="form-control" style="overflow:hidden"
                    :rows="responsibilities[index].length < 68 ? 1 : 2"
                    :ref="'respItem-' + index"
                    @focus="textAreaAdjust(index)"
                    @keyup="onChangeResponsibilities(index)"
                    v-for="(res, index) in responsibilities"
                    :key="index"
                    v-model="responsibilities[index]"
                    placeholder="Add Another Responsibility">
                </textarea>
            </form>
        </template>

        <template slot="custom-modal-footer">
            <div class="btn btn-link btn-delete" data-dismiss="modal" v-if="current != -1" @click="deleteRecord">
                Delete
            </div>

            <button class="pull-right save-changes" type="submit" @click="submit" :disabled="disabled">
                Save Changes
            </button>

            <button class="pull-right mr-2" style="width: auto; padding-right: 10px; padding-left: 10px" type="submit" @click="submit('add more')" :disabled="disabled" v-if="current == -1">
                Add more work history
            </button>

        </template>

    </main-modal>
</template>

<script>
    import Api from '@/api';
    import MainModal from '../common/MainModal';
    import AlertEmployment from '../common/AlertEmployment';

    export default {
        name: "employment-modal",
        data() {
            return {
                isDisplayAlert: false,
                has_focus_role: false,
                has_focus_company: false,
                has_focus_location: false,
                disabled: false,
                months: Utils.getMonths(),
                years: Utils.getYears(),
                current: -1,
                job_roles: [],
                locations: [],
                companies: [],
                time_out: false,
                /**
                 * Save Input
                 */
                id: '',
                job_id: '',
                company_id: '',
                job_role: '',
                company_name: '',
                location: '',
                project_size: '',
                isCurrent: '',
                start_month: '',
                start_year: '',
                end_month: '',
                end_year: '',
                responsibilities: [],
                errors: {
                    job_role: '', company_name: '', location: '', project_size: '', isCurrent: '',
                    start_month: '', start_year: '', end_month: '', end_year: '', responsibilities: [],
                },
                endpoints: {
                    save: '/api/v1/work/experience',
                    delete: '/api/v1/work/experience/',
                },
                timeoutHandler: null,
                leaveTimeoutHandler: null,
                numberRange: [],
                duration_number: '',
                duration_type: ''
            }
        },
        created() {
            let vm = this;

            Bus.$on('showEmployment', function(index, details) {

                vm.current = index;
                vm.isDisplayAlert = vm.current == -1 ? true : false;
                vm.setValues(details);
                vm.createRange();
            });
        },
        methods: {
            createRange() {
                this.numberRange = [];
                for( let i = 1; i <= 31; i++)
                    this.numberRange.push(i);
            },
            hasFocus() {
                this.has_focus_role = false;
                this.has_focus_company = false;
                this.has_focus_location = false;

                this.clearOptionsSelection();
            },
            hasFocusRole(has_focus) {
                this.has_focus_role = has_focus;

                if (has_focus) {
                    this.has_focus_company = false;
                    this.has_focus_location = false;
                }

                this.clearOptionsSelection();
            },
            hasFocusCompany(has_focus) {
                this.has_focus_company = has_focus;

                if (has_focus) {
                    this.has_focus_role = false;
                    this.has_focus_location = false;
                }

                this.clearOptionsSelection();
            },
            hasFocusLocation(has_focus) {
                this.has_focus_location = has_focus;

                if (has_focus) {
                    this.has_focus_role = false;
                    this.has_focus_company = false;
                }

                this.clearOptionsSelection();
            },
            clearOptionsSelection() {

                this.job_roles = [];
                this.locations = [];
                this.companies = [];
            },
            setValues(details) {
                this.id = details ? details.id : '';
                this.company_id = details ? details.company_id: '';
                this.job_role = details ? details.job_role : '';
                this.company_name = details ? (details.company_id && details.company ? details.company.name : details.company_name) : '';
                this.location = details ? (details.location ? details.location : (details.company ? details.company.address : '')) : '';
                this.project_size = details ? details.project_size : '';
                this.isCurrent = details ? details.isCurrent : '';
                this.start_month = details ? details.start_month : '';
                this.start_year = details ? details.start_year : '';
                this.end_month = details ? details.end_month : '';
                this.end_year = details ? details.end_year : '';
                this.responsibilities = details ? details.responsibilities : [];
                this.duration_number = details ? details.duration_number : 1;
                this.duration_type = details ? details.duration_type : 'weeks';

                // if (this.company_id) {
                //     this.$refs['locationRef'].disabled = true;
                // }

                this.responsibilities = this.responsibilities.filter(r => r!=='');
                this.responsibilities.push('');
            },
            close() {
                this.current = -1;

                Utils.setObjectValues(this.errors, '');
            },
            textAreaAdjust(index) {
                this.hasFocus();

                let o = index == -1 ? this.$refs['respItem-' + index] : this.$refs['respItem-' + index][0];
                o.style.height = '1px';
                o.style.height = (2 + o.scrollHeight) + 'px';
            },
            onChangeCurrentRole() {
                if (this.isCurrent == 1) {
                    this.end_year = null;
                    this.end_month = null;
                }
            },
            onChangeResponsibilities(index) {
                this.textAreaAdjust(index);

                this.responsibilities = this.responsibilities.filter(r => r!=='');
                this.responsibilities.push('');
            },
            async onChangeLocation(keyword) {


                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler)
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
            async onSearchJob(keyword) {

                this.job_id = '';

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler)
                }

                this.timeoutHandler = await setTimeout(() => {

                    Api.searchJobRoles(keyword).then((result) => {

                        this.job_roles = result.data.job_roles;
                    });

                }, 400);
                //this.job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];
            },
            onLeave(type) {

                if (this.leaveTimeoutHandler) {

                    clearTimeout(this.leaveTimeoutHandler)
                }

                this.leaveTimeoutHandler = setTimeout(() => {

                    this.job_roles = [];
                }, 2000)
            },
            async onSearchCompany(keyword) {

                this.company_id = '';

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler)
                }

                if (keyword && keyword.length > 0) {

                    this.timeoutHandler = await setTimeout(() => {

                        Api.getCompaniesPromise(keyword).then((data) => {
                            this.companies = data.data ? data.data.companies : [];
                        });

                    }, 400);

                } else {

                    this.locations = [];
                }

            },
            onSelectJob(job) {
                this.job_id = job.id;
                this.job_role = job.job_role_name;

                this.job_roles = [];
            },
            onSelectLocation(location) {
                this.location = location;

                this.locations = [];
            },
            onSelectCompany(company) {
                this.company_id = company.id;
                this.company_name = company.name;
                this.location = company.address;
                // this.$refs['locationRef'].disabled = true;

                this.companies = [];
            },
            deleteRecord() {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteEmployment', this.current, this.endpoints.delete + this.id);
            },
            async submit(mode) {

                let vm = this;
                let saveEndpoint = this.id == 0 ? this.endpoints.save : this.endpoints.save + '/' + this.id;

                for (let i = 0; i < this.responsibilities.length; i++) {
                    this.responsibilities[i] = this.$refs['respItem-' + i][0].value;
                }

                if (! this.end_year && ! this.end_month) {
                    this.isCurrent = 1;
                }

                if( this.project_size == null)
                    this.project_size = '';

                let saveInput = {
                    job_id: this.job_id,
                    company_id: this.company_id,
                    job_role: this.job_role,
                    company_name: this.company_name,
                    location: this.location,
                    project_size: this.project_size,
                    isCurrent: this.isCurrent,
                    start_month: this.start_month,
                    start_year: this.start_year,
                    end_month: this.end_month,
                    end_year: this.end_year,
                    responsibilities: this.responsibilities,
                    duration_number: this.duration_number,
                    duration_type: this.duration_type,
                };

                Utils.setObjectValues(this.errors, '');

                this.disabled = true;

                await axios.post(saveEndpoint, saveInput, Utils.getBearerAuth())

                .then(function(response) {

                    let data = response.data;

                    $('#modalEmployment').modal('hide');

                    if (mode === 'add more') {

                        Bus.$emit('addAnotherEmployment');
                        Bus.$emit('alertSuccessEmployment', data.message);
                    }

                    Bus.$emit('updateEmployment', vm.current, data.data.work_experience);

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                this.disabled = false;
            },
            formatCurrency(field, e, blur = null) {
                this[field] = Utils.formatCurrency(e, blur);
            },
        },
        components: {
            MainModal,
            AlertEmployment
        },
    }
</script>
