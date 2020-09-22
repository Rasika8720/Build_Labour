<template>
    <div class="form-group" v-if="record">

        <div class="record-title">
            {{ record.full_name }}
        </div>
        <!-- <ul class="record-links">
            <li class="active">Info</li>
            <li class="inactive">Chats</li>
            <li class="inactive">Something</li>
        </ul> -->
        <div class="record-photo">
            <div class="record-profile-pic">
                <img v-if="record.profile_photo_url" :src="record.profile_photo_url" height="120px" />
                <img v-else src="/img/defaults/user.png" height="120px" />
            </div>

            <input type="file" id="upload" value="Choose a file" accept="image/*" style="display:none" @change="onFileChange" />

            <div class="record-photo-actions">
                <div class="action-item" @click="onClickUploadImage">
                    <span class="fa fa-upload bl-ml-35 bl-mr-15 bl-mb-32"></span>
                    Upload Image
                </div>
                <div class="action-item" data-toggle="modal" data-target="#deleteRecordModal" @click="onClickDeleteImage">
                    <span class="fa fa-trash bl-ml-35 bl-mr-15"></span>
                    Delete Image
                </div>
            </div>
        </div>
        <div class="me-label" style="margin-bottom:17px">User Details</div>
        <div class="me-row">
            <div class="role-col-left">
                <div class="emp-form-label">First Name</div>
                <input class="form-control" type="text" v-model="profile.first_name"
                    @focus="hasFocus()" />
                <span class="err-msg" v-if="errors.first_name">{{ errors.first_name }}</span>
            </div>
            <div class="role-col-right">
                <div class="emp-form-label">Last Name</div>
                <input class="form-control" type="text" v-model="profile.last_name"
                    @focus="hasFocus()" />
                <span class="err-msg" v-if="errors.last_name">{{ errors.last_name }}</span>
            </div>
        </div>
        <div class="emp-row mt-3">
            <div class="role-col-left">
                <div class="emp-form-label">Gender</div>
                <select class="form-control" v-model="about_me_general.gender">
                    <option key="1" value="Male">Male</option>
                    <option key="2" value="Female">Female</option>
                    <option key="3" value="">Other</option>
                </select>
                <span class="err-msg" v-if="errors.gender">
                    {{ errors.gender }}
                </span>
            </div>
            <div class="role-col-right">
                <div class="emp-form-label">Email</div>
                <input class="form-control" type="text" v-model="profile.email"
                       @focus="hasFocus()" />
                <span class="err-msg" v-if="errors.email">{{ errors.email }}</span>
            </div>
        </div>
        <div class="me-label" style="margin-bottom:17px">Date of Birth (optional)</div>
        <div class="me-row">
            <div class="me-col-mid">
                <div class="emp-form-label">Month</div>
                <select class="form-control" v-model="birthMonth" @change="onChangeBirthMonthYear">
                    <option v-for="(month, index) in months" :key="index" v-bind:value="month.id">{{ month.name }}</option>
                </select>
            </div>
            <div class="me-col-left">
                <div class="emp-form-label">Day</div>
                <select class="form-control" v-model="birthDay">
                    <option v-for="(day, index) in days" :key="index" v-bind:value="day">{{ day }}</option>
                </select>
            </div>
            <div class="me-col-right">
                <div class="emp-form-label">Year</div>
                <select class="form-control" v-model="birthYear" @change="onChangeBirthMonthYear">
                    <option v-for="(year, index) in years" :key="index" v-bind:value="year">{{ year }}</option>
                </select>
            </div>
        </div>
        <span class="err-msg" v-if="errors.date_of_birth">
            {{ errors.date_of_birth }}
        </span>
        <div class="emp-row">
            <div class="modal-form-label">Location</div>
            <input class="form-control" type="text" v-model="profile.address"
                @focus="hasFocusLocation(true)"
                @keyup="onChangeLocation(profile.address)" />
        </div>
        <div class="emp-row" style="margin-top:0" v-if="has_focus_location && locations && locations.length > 0">
            <ul class="list-group">
                <li class="list-group-item" v-for="(place, idx) in locations" :key="idx"
                    @click="onSelectLocation(place.place_name)">
                    {{ place.place_name }}
                </li>
            </ul>
        </div>
        <div class="skill-label">Summary of career</div>
        <textarea rows="3" ref="userIntro" class="form-control" style="overflow:hidden"
            placeholder="Example: Experienced Senior Project Manager; demonstrated history of working on a wide range of construction projects for leading companies."
            @focus="textAreaAdjust('userIntro')"
            @keyup="textAreaAdjust('userIntro')"
            v-model="profile.profile_description">
        </textarea>
        <div class="skill-label">Education</div>
        <div class="me-row">
            <select v-model="profile.education_id" @focus="hasFocus()">
                <option value="" disabled selected v-if="educations.length == 0">No education added yet</option>
                <option v-for="(education, index) in educations" :key="index" v-bind:value="education.id">
                    {{ education.course ? education.course.course_name: education.course_name }}
                </option>
            </select>
        </div>
        <div class="me-label">Most Recent Role</div>
        <div class="emp-row mt-3">
            <input class="form-control" type="text" placeholder="Most Recent Role" v-model="profile.most_recent_role"
                @focus="hasFocusRole(true)"
                @keyup="onSearchJob(profile.most_recent_role)" />
        </div>
        <div class="emp-row" style="margin-top:0" v-if="has_focus_role && job_roles && job_roles.length > 0">
            <ul class="list-group">
                <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                    @click="onSelectJob(job)">

                    {{ job.job_role_name }}
                </li>
            </ul>
        </div>

        <div class="me-row">
            <div class="role-col-left">
                <label class="record-label">
                    Year(s)
                </label>
                <input class="form-control" type="number" placeholder="Years" v-model="profile.exp_year"
                    @focus="hasFocus()" />
                <span class="err-msg" v-if="errors.exp_year">{{ errors.exp_year }}</span>
            </div>

            <div class="role-col-right">
                <label class="record-label">
                    Month(s)
                </label>
                <input class="form-control" type="number" placeholder="Months" v-model="profile.exp_month"
                    @focus="hasFocus()" />
                <span class="err-msg" v-if="errors.exp_month">{{ errors.exp_month }}</span>

            </div>
        </div>
        <div class="skill-label">
            Areas of construction
        </div>
        <div class="bl-inline" v-for="(sector, index) in sectors_list" :key="index">
            <input :id="'sector-styled-checkbox-'+index" class="styled-checkbox" type="checkbox"
                :value="sector.id" v-model="profile.sectors"
                @focus="hasFocus()" />

            <label :for="'sector-styled-checkbox-'+index" style="width:125px">{{ sector.business_type }}</label>
        </div>
        <div class="skill-label">
            Tiers
        </div>
        <div class="bl-inline" v-for="(tier, index) in tiers_list" :key="index + sectors_list.length">
            <input :id="'tier-styled-checkbox-'+index" class="styled-checkbox" type="checkbox"
                :value="tier.id" v-model="profile.tiers"
                @focus="hasFocus()" />

            <label :for="'tier-styled-checkbox-'+index">{{ tier.tier_name }}</label>
        </div>
        <div class="me-label">Drivers Licence</div>
        <div class="me-row">
            <div class="role-col-left">
                <div class="emp-form-label">State</div>
                <select class="form-control" v-model="about_me_technical.drivers_license_state">
                    <option value="none">none</option>
                    <option value="NSW">New South Wales</option>
                    <option value="QLD">Queensland</option>
                    <option value="SA">South Australia</option>
                    <option value="TAS">Tasmania</option>
                    <option value="VIC">Victoria</option>
                    <option value="WA">Western Australia</option>

                </select>
            </div>
            <div class="role-col-right">
                <div class="emp-form-label">Type</div>
                <select class="form-control" v-model="about_me_technical.drivers_license_type">
                    <option value="none">none</option>
                    <option value="Full">Full</option>
                    <option value="Probationary">Probationary</option>
                </select>
            </div>
        </div>
        <div class="me-label">Australian Tax File No. (TFN) (optional)</div>
        <div class="emp-row mt-3">
            <input class="form-control" type="text" placeholder="TFN" v-model="about_me_technical.australian_tfn "/>
        </div>
        <div class="skill-label">
            Has the right to legally work in Australia
        </div>
        <div class="skill-label-3">
            <a href="https://www.australia.gov.au/information-and-services/immigration-and-visas/work-visas" target="_blank">See legal requirements</a>
        </div>
        <div class="bl-inline">
            <input id="right_to_work_1" class="styled-checkbox-round" type="checkbox"
                ref="right_to_work_1" @change="formatCheckbox('right_to_work', 1)" />
            <label for="right_to_work_1">Yes</label>

            <input id="right_to_work_0" class="styled-checkbox-round" type="checkbox"
                ref="right_to_work_0" @change="formatCheckbox('right_to_work', 0)" />
            <label for="right_to_work_0">No</label>
        </div>
        <div class="me-label">
            Have an Australian Business Number (ABN)
        </div>
        <div class="bl-inline">
            <input id="has_abn_1" class="styled-checkbox-round" type="checkbox"
                ref="has_abn_1" @change="formatCheckbox('has_abn', 1)" />
            <label for="has_abn_1">Yes</label>

            <input id="has_abn_0" class="styled-checkbox-round" type="checkbox"
                ref="has_abn_0" @change="formatCheckbox('has_abn', 0)" />
            <label for="has_abn_0">No</label>
        </div>

        <div class="me-label">
            Competent in WRITTEN and SPOKEN english
        </div>
        <div class="bl-inline">
            <input id="english_skill_1" class="styled-checkbox-round" type="checkbox"
                ref="english_skill_1" @change="formatCheckbox('english_skill', 1)" />
            <label for="english_skill_1">Yes</label>

            <input id="english_skill_0" class="styled-checkbox-round" type="checkbox"
                ref="english_skill_0" @change="formatCheckbox('english_skill', 0)" />
            <label for="english_skill_0">No</label>
        </div>
        <div class="me-label">
            Own/have access to a registered vehicle on a permanent basis
        </div>
        <div class="bl-inline">
            <input id="has_registered_vehicle_1" class="styled-checkbox-round" type="checkbox"
                ref="has_registered_vehicle_1" @change="formatCheckbox('has_registered_vehicle', 1)" />
            <label for="has_registered_vehicle_1">Yes</label>

            <input id="has_registered_vehicle_0" class="styled-checkbox-round" type="checkbox"
                ref="has_registered_vehicle_0" @change="formatCheckbox('has_registered_vehicle', 0)" />
            <label for="has_registered_vehicle_0">No</label>
        </div>
        <div class="skill-label">
            Distance willing to travel from home for employment.
        </div>
        <div class="emp-row-2 row-center">
            <div class="emp-col-left-2">
                <div class="bl-slider">
                    <input type="range" min="0" max="500" step="5" class="slider" :style="maxDistance" v-model="work_preferences.max_distance">
                </div>
            </div>
            <div class="emp-col-right-2">
                <label>
                    {{ work_preferences.max_distance }} km
                </label>
            </div>
        </div>
        <div class="skill-label">
            Willing work/relocate to another state? If Yes, tick states that apply.
        </div>

        <div class="bl-inline" v-for="(state, index) in states" :key="state+index">
            <input class="styled-checkbox" :id="'styled-checkbox-'+index" type="checkbox"
                v-bind:value="state" v-model="work_preferences.selected" />
            <label :for="'styled-checkbox-'+index">{{ state }}</label>
        </div>
        <div class="skill-label">
            What is your ideal next role?
        </div>
        <textarea rows="3" ref="idealIntro" class="form-control" style="overflow:hidden"
            placeholder="Example: My ideal next role would be as a qualified plumber working on high-end residential jobs with an awesome team."
            @focus="textAreaAdjust('idealIntro')"
            @keyup="textAreaAdjust('idealIntro')"
            v-model="work_preferences.introduction">
        </textarea>
        <div class="me-label">
            Ideally this would happen in:
        </div>
        <div class="me-row">
            <div class="role-col-left">
                <select v-model="work_preferences.when">
                    <option key="0" value="0">Now</option>
                    <option key="1" value="3">3 months</option>
                    <option key="2" value="6">6 months</option>
                    <option key="3" value="9">9 months</option>
                    <option key="4" value="12">12 months</option>
                    <option key="5" value="">Rather not say</option>
                </select>
                <span class="err-msg" v-if="errors.when">
                    {{ errors.when }}
                </span>
            </div>
        </div>
        <div class="me-label">
            Would you relocate overseas?
        </div>
        <div class="bl-inline">
            <input id="willing_to_relocate_1" class="styled-checkbox-round" type="checkbox"
                ref="willing_to_relocate_1" @change="formatRelocateCheckbox('willing_to_relocate', 1)" />
            <label for="willing_to_relocate_1">Yes</label>

            <input id="willing_to_relocate_0" class="styled-checkbox-round" type="checkbox"
                ref="willing_to_relocate_0" @change="formatRelocateCheckbox('willing_to_relocate', 0)" />
            <label for="willing_to_relocate_0">No</label>
        </div>

        <div class="me-label" v-if="work_preferences.willing_to_relocate">Select up to three countries:</div>
        <div class="me-row mb-3" v-if="work_preferences.willing_to_relocate">
            <div class="role-col-left">
                <select v-model="work_preferences.selected_countries[0]" class="mb-3" style="width:140%">
                    <option v-for="(country, index) in countries" :key="index" :value="country">{{ country }}</option>
                </select>

                <select v-model="work_preferences.selected_countries[1]" class="mb-3" style="width:140%">
                    <option v-for="(country, index) in countries" :key="index" :value="country">{{ country }}</option>
                </select>

                <select v-model="work_preferences.selected_countries[2]" class="mb-3" style="width:140%">
                    <option v-for="(country, index) in countries" :key="index" :value="country">{{ country }}</option>
                </select>
            </div>
        </div>
        <div class="form-group row mt-5">
            <div class="btn btn-link btn-delete mb-3" style="margin-right:180px" data-toggle="modal" data-target="#deleteRecordModal" @click="onClickDeleteAccount()">
                    Delete Worker Account
            </div>
            <div class="col-md-6">
                <button type="submit" @click="submit" :disabled="disabled">
                    {{ loading ? '. . .' : 'Save Changes' }}
                </button>
                <div class="loading">
                    <pulse-loader :loading="loading" color="#fff" size="8px"></pulse-loader>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "view-worker",
		data() {
			return {
                loading:false,
                record_id: 0,
                record: null,
                disabled: false,
				endpoints: {
					get: ''
                },
                errors: {
                    profile_description: '', first_name: '', last_name: '', address: '', education_id: '',
                    most_recent_role: '', exp_year: '', exp_month: '', tiers: '', sectors: '',
                },
                has_focus_location: false,
                has_focus_role: false,
                locations: [],
                job_roles: [],
                sectors:[],
                countries:[],
                profile: {
                    profile_description: '', profile_photo_url: '', first_name: '', last_name: '', email: '', is_verified: '',
                    company_name: '', job_role: '', sectors: [], tiers: [], address: '', education_id: '',
                    most_recent_role: '', exp_year: '', exp_month: '',user_id:'',
                },
                about_me_general: {
                    gender: '', date_of_birth: '', country_birth: '', marital_status: '',user_id:'' ,
                },
                about_me_technical: {
                    right_to_work: '', has_tfn: '', has_abn: '',
                    english_skill: '', drivers_license: '', drivers_license_state: '', drivers_license_type: '', has_registered_vehicle: '',
                    australian_tfn: '',user_id:'',
                },
                ideal_role: {
                    introduction: '', when: '', max_distance: '', state: '', right_to_work: '',
                },
                employments: [],
                educations: [],
                tickets: [],
                industry_skills: [],
                sectors_list: [
                    { id: 1, business_type: 'Residential' },
                    { id: 2, business_type: 'Commercial' },
                    { id: 3, business_type: 'Civil' },
                ],
                tiers_list: [
                    { id: 1, tier_name: 'Tier 1' },
                    { id: 2, tier_name: 'Tier 2' },
                    { id: 3, tier_name: 'Tier 3' },
                    { id: 4, tier_name: 'Tier 4' },
                ],
                endpoints: {
                    get: '/api/v1/auth/user',
                    get_user: '/api/v1/worker/view/'
                },
                days: [],
                months: Utils.getMonths(),
                years: Utils.getYears(new Date().getFullYear() - 18),
                birthMonth: '',
                birthYear: '',
                birthDay: '',
                work_preferences: {
                    max_distance: 0, state: '', selected: [], introduction: '', when: '',
                    willing_to_relocate: '', countries: '', selected_countries: [],user_id:'',
                },
                states: [
                    'QLD', 'NSW', 'SA', 'VIC', 'WA', 'ACT', 'TAS', 'NT',
                ],


			}
		},
        mounted(){
            let vm = this;

            Bus.$on('datatableViewWorker', function(id){
                vm.record_id = id;
                vm.endpoints.get = '/api/v1/admin/user/get?id=' + id;
                vm.viewRecord();

                setTimeout(function() { vm.getUser(); }, 1000);

            });

            Bus.$on('croppedPhoto', function (profile_photo_url) {
                vm.record.profile_photo_url = profile_photo_url;
            });

            Bus.$on('closePhotoModal', function() {
                $('#upload').val('');
            });

            this.days = Utils.getDaysInMonth(this.birthMonth, this.birthYear);
            this.getCountries();
            Bus.$on('removeWorker', function() {
                location.reload();
            });

        },
		methods: {
		  	viewRecord() {
				let vm = this;

				axios.get(vm.endpoints.get, Utils.getBearerAuth())

                .then(function(response) {

                    vm.record = response.data.data.record;

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },
            hasFocus() {
                this.has_focus_role = false;
                this.has_focus_location = false;
            },
            formatDate(d) {
                if (d != null) {
                    let date = new Date(d);

                    return date.getDate() + ' ' + Utils.getMonth(date.getMonth()) + ' ' + date.getFullYear();
                }
			},
            onClickDeleteAccount() {
				Bus.$emit('deleteWorker', this.record_id);
            },
            onClickUploadImage() {
                upload.click();
            },
            onFileChange(e) {
                Utils.onFileChange(e, this.record_id, 'Admin');
            },
            onClickDeleteImage() {
                Bus.$emit('deletePhoto', this.record_id);
            },
            hasFocusLocation(has_focus) {
                this.has_focus_location = has_focus;

                if (has_focus) {
                    this.has_focus_role = false;
                }
            },
            onChangeLocation(keyword) {
                this.locations = (keyword && keyword.length > 0) ? Api.getLocations(keyword) : [];
            },
            onSelectLocation(location) {
                this.record.address = location;

                this.locations = [];
            },
            textAreaAdjust(refName) {
                this.hasFocus();

                Utils.textAreaAdjust(this.$refs[refName]);
            },
            async getUser() {
                let vm = this;

                await axios.get(vm.endpointGet, Utils.getBearerAuth())

                .then(function(response) {
                    let user = response.data.data.user;
                    vm.profile = {};
                    vm.profile.user_id =user.id
                    vm.profile.profile_photo_url = user.profile_photo_url;
                    vm.profile.profile_description = user.worker_detail ? user.worker_detail.profile_description : '';
                    vm.profile.first_name = user.first_name;
                    vm.profile.last_name = user.last_name;
                    vm.profile.email = user.email;
                    vm.profile.is_verified = user.is_verified;
                    vm.profile.address = user.address;
                    vm.profile.education_id = user.worker_detail.education_id;
                    vm.profile.education = user.worker_detail.education;
                    vm.profile.sectors = [];
                    vm.sectors = user.worker_detail.sectors;
                    vm.profile.tiers = [];
                    vm.profile.most_recent_role = user.worker_detail.most_recent_role;
                    vm.profile.exp_year = user.worker_detail.exp_year;
                    vm.profile.exp_month = user.worker_detail.exp_month;
                    vm.profile.job_role = (user.experiences[0] && user.experiences[0].job) ?
                                                    user.experiences[0].job.title :
                                                    (user.experiences[0]) ? user.experiences[0].job_role : '';
                    vm.profile.company_name = (user.experiences[0] && user.experiences[0].company) ?
                                                        user.experiences[0].company.name :
                                                        (user.experiences[0]) ? user.experiences[0].company_name : '';

                    if (! Utils.isNullOrEmpty(user.date_of_birth)) {
                        let d = new Date(user.date_of_birth);

                        vm.birthDay = d.getDate();
                        vm.birthMonth = d.getMonth() + 1;
                        vm.birthYear = d.getFullYear();

                        vm.days = Utils.getDaysInMonth(vm.birthMonth, vm.birthYear);

                    }
                    vm.about_me = {};
                    vm.about_me_general.gender = user.gender;
                    vm.about_me_general.date_of_birth = user.date_of_birth;
                    vm.about_me_general.country_birth = user.country_birth;
                    // vm.about_me_general.marital_status = user.marital_status;

                    vm.about_me_technical = {};
                    vm.about_me_technical.user_id = user.id;
                    vm.about_me_technical.right_to_work = user.worker_detail.right_to_work ? user.worker_detail.right_to_work : 0;
                    vm.about_me_technical.has_tfn = user.worker_detail.has_tfn ? user.worker_detail.has_tfn : 0;
                    vm.about_me_technical.has_abn = user.worker_detail.has_abn ? user.worker_detail.has_abn : 0;
                    vm.about_me_technical.english_skill = user.worker_detail.english_skill ? user.worker_detail.english_skill : 0;
                    vm.about_me_technical.drivers_license = user.worker_detail.drivers_license ? user.worker_detail.drivers_license : 0;
                    vm.about_me_technical.drivers_license_state = user.worker_detail.drivers_license_state;
                    vm.about_me_technical.drivers_license_type = user.worker_detail.drivers_license_type;
                    vm.about_me_technical.australian_tfn = user.worker_detail.australian_tfn;
                    vm.about_me_technical.has_registered_vehicle = user.worker_detail.has_registered_vehicle ? user.worker_detail.has_registered_vehicle : 0;





                    vm.work_preferences.introduction = user.worker_detail.introduction;
                    vm.work_preferences.user_id = user.id;
                    vm.work_preferences.when = user.worker_detail.when;
                    vm.work_preferences.max_distance = user.worker_detail.max_distance && user.worker_detail.max_distance != '' ? user.worker_detail.max_distance : 0;
                    vm.work_preferences.willing_to_relocate = user.worker_detail.willing_to_relocate;
                    vm.work_preferences.state = user.worker_detail.state;
                    vm.work_preferences.countries = user.worker_detail.user.country;
                    vm.work_preferences.selected = vm.work_preferences.state ? vm.work_preferences.state.split(',') : [];
                    vm.work_preferences.selected_countries = vm.work_preferences.countries ? vm.work_preferences.countries.split(',') : [];


                    vm.employments = user.experiences;
                    vm.educations = user.educations;
                    vm.tickets = user.tickets;
                    vm.industry_skills = user.skills;

                    for (let i = 0; i < user.worker_detail.sectors.length; i++) {

                        vm.profile.sectors.push(user.worker_detail.sectors[i].id);
                    }

                    for (let i = 0; i < user.worker_detail.tiers.length; i++) {
                        vm.profile.tiers.push(user.worker_detail.tiers[i].id);
                    }

                        vm.formatCheckbox('right_to_work', vm.about_me_technical.right_to_work);

                    vm.formatCheckbox('has_abn', vm.about_me_technical.has_abn);
                    vm.formatCheckbox('english_skill', vm.about_me_technical.english_skill);

                    vm.formatCheckbox('has_registered_vehicle', vm.about_me_technical.has_registered_vehicle);
                    vm.formatRelocateCheckbox('willing_to_relocate', user.worker_detail.willing_to_relocate);


                }).catch(function(error) {
                    /**
                     * NOTE: Please do not delete token if error occurs
                     * Instead, this should be deleted if unauthorized access is returned
                     */
                    // Api.deleteToken();

                    Utils.handleError(error);
                });
            },
            onSearchJob(keyword) {
                this.job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];
            },
            hasFocusRole(has_focus) {
                this.has_focus_role = has_focus;

                if (has_focus) {
                    this.has_focus_location = false;
                }
            },
            onSelectJob(job) {
                this.profile.most_recent_role = job.job_role_name;

                this.job_roles = [];
            },
            async submit(){
                let vm = this;

                Utils.setObjectValues(this.errors, '');


                vm.loading = true;
                vm.setAboutMeGeneralValues();
                vm.work_preferences.state = vm.work_preferences.selected.toString();
                vm.work_preferences.countries = vm.work_preferences.selected_countries.length > 0 ? vm.work_preferences.selected_countries.toString() : null;

                Api.submit('/api/v1/admin/worker/sectors', { sectors: this.profile.sectors, tiers: this.profile.tiers, user_id:this.record_id });
                Api.submit('/api/v1/admin/worker/next-role', vm.$data.work_preferences);

                await axios.post('/api/v1/admin/worker/introduction', vm.$data.profile, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    vm.sectors = [];
                    vm.tiers = [];

                    for (let i = 0; i < vm.profile.sectors.length; i++) {
                        vm.sectors.push(vm.sectors_list.find(obj => obj['id'] == vm.profile.sectors[i]));
                    }

                    for (let i = 0; i < vm.profile.tiers.length; i++) {
                        vm.tiers.push(vm.tiers_list.find(obj => obj['id'] == vm.profile.tiers[i]));
                    }

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                await axios.post('/api/v1/admin/worker/personal-details', vm.$data.about_me_general, Utils.getBearerAuth())

                .then(function(response) {


                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                await axios.post('/api/v1/admin/worker/affirmations', vm.$data.about_me_technical, Utils.getBearerAuth())

                .then(function(response) {

                    window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
                    Bus.$emit('alertSuccess', response.data.message);

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                vm.loading = false;

            },
            onChangeBirthMonthYear() {
                this.days = Utils.getDaysInMonth(this.birthMonth, this.birthYear);
            },
            setAboutMeGeneralValues() {
                let vm = this;
                vm.about_me_general.user_id = vm.record_id;
                vm.about_me_technical.user_id = vm.record_id;
                vm.about_me_general.date_of_birth = vm.birthYear + '-' + vm.birthMonth + '-' + vm.birthDay;

                if (! vm.birthYear && ! vm.birthMonth && ! vm.birthDay) {
                    vm.about_me_general.date_of_birth = '';
                }
            },
            formatCheckbox(refName, value) {

                if (value == 1 ) {
                this.$refs[refName + '_1'].checked = true;
                this.$refs[refName + '_0'].checked = false;

                } else if (value == 0 ) {
                    this.$refs[refName + '_1'].checked = false;
                    this.$refs[refName + '_0'].checked = true;


                } else {
                    this.$refs[refName + '_1'].checked = false;
                    this.$refs[refName + '_0'].checked = false;

                }
                this.about_me_technical[refName] = value;

            },
             async formatRelocateCheckbox(refName, value) {
                if (value == 1) {
                this.$refs[refName + '_1'].checked = true;
                this.$refs[refName + '_0'].checked = false;

                } else if (value == 0) {
                    this.$refs[refName + '_1'].checked = false;
                    this.$refs[refName + '_0'].checked = true;

                } else {
                    this.$refs[refName + '_1'].checked = false;
                    this.$refs[refName + '_0'].checked = false;
                }
                this.work_preferences[refName] = value;

            },
            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },
            getCountries() {
                let vm = this;

                Promise.resolve(Api.getCountries()).then(function(data) {
                    vm.countries = data.data.countries;
                });
            },

        },
        computed: {
            endpointGet() {
                return this.record_id ? this.endpoints.get_user + this.record_id : this.endpoints.get;
            },
            maxDistance() {
                return {
                    background: `linear-gradient(to right, #00aeef ${(this.work_preferences.max_distance / 500) * 100}%,
                                #00aeef ${(this.work_preferences.max_distance / 500) * 100}%, #fff 00%, #fff 100%)`
                }
            },
        },
        components: {
			PulseLoader,
		},
	}
</script>
