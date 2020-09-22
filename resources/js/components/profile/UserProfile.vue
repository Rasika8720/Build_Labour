<template>
    <div class="profile-item-1">
        <div class="profile-content">

            <photo-modal></photo-modal>

            <main-modal id="modalUserProfile">

                <template slot="custom-modal-title">
                    <h4 class="modal-title">Edit Introduction</h4>
                    <div class="close" data-dismiss="modal" @click="close">&times;</div>
                </template>

                <template slot="custom-modal-content">
                    <form class="modal-form" method="POST" @submit.prevent="submit">
                        <div class="me-label" style="margin-bottom:17px">User Details</div>
                        <div class="me-row">
                            <div class="role-col-left">
                                <div class="emp-form-label">First Name</div>
                                <input class="form-control" type="text" v-model="input.first_name"
                                    @focus="hasFocus()" />
                                <span class="err-msg" v-if="errors.first_name">{{ errors.first_name }}</span>
                            </div>
                            <div class="role-col-right">
                                <div class="emp-form-label">Last Name</div>
                                <input class="form-control" type="text" v-model="input.last_name"
                                    @focus="hasFocus()" />
                                <span class="err-msg" v-if="errors.last_name">{{ errors.last_name }}</span>
                            </div>
                        </div>

                        <div class="emp-row">
                            <div class="modal-form-label">Location</div>
                            <input class="form-control" type="text" v-model="input.address"
                                @focus="hasFocusLocation(true)"
                                @keyup="onChangeLocation(input.address)" />
                        </div>

                        <div class="emp-row" style="margin-top:0" v-if="has_focus_location && locations && locations.length > 0">
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(place, idx) in locations" :key="idx"
                                    @click="onSelectLocation(place.place_name)">
                                    {{ place.place_name }}
                                </li>
                            </ul>
                        </div>

                        <div class="skill-label">Summary of Career</div>
                        <textarea rows="3" ref="userIntro" class="form-control" style="overflow:hidden" id="userIntro"
                            placeholder="Example: Experienced Senior Project Manager; demonstrated history of working on a wide range of construction projects for leading companies."
                            @focus="textAreaAdjust('userIntro')"
                            @keyup="textAreaAdjust('userIntro')"
                            v-model="input.profile_description"
                            maxlength="300">
                        </textarea>
                        <label for="userIntro" class="description" style="font-size: 14px; color: red; font-style: italic;">Max 300 characters</label>

                        <div class="skill-label">Education</div>
                        <div class="me-row">
                            <select v-model="input.education_id" @focus="hasFocus()">
                                <option value="" disabled selected v-if="educations.length == 0">No education added yet</option>
                                <option v-for="(education, index) in educations" :key="index" v-bind:value="education.id">
                                    {{ education.course ? education.course.course_name: education.course_name }}
                                </option>
                            </select>
                        </div>

                        <div class="me-label">
                            Most Recent Role
                        </div>
                        <div class="emp-row mt-3">
                            <input class="form-control" type="text" placeholder="Most Recent Role" v-model="input.most_recent_role"
                                @focus="hasFocusRole(true)"
                                @keyup="onSearchJob(input.most_recent_role)" />
                        </div>

                        <div class="emp-row" style="margin-top:0" v-if="has_focus_role && job_roles && job_roles.length > 0">
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                                    @click="onSelectJob(job)">

                                    {{ job.job_role_name }}
                                </li>
                            </ul>
                        </div>

                        <div class="me-label" style="margin-bottom:17px">Years Experience</div>
                        <div class="me-row">
                            <div class="role-col-left">
                                <input class="form-control" type="number" placeholder="Years" v-model="input.exp_year"
                                    @focus="hasFocus()" />
                                <span class="err-msg" v-if="errors.exp_year">{{ errors.exp_year }}</span>
                            </div>

                            <div class="role-col-right">
                                <input class="form-control" type="number" placeholder="Months" v-model="input.exp_month"
                                    @focus="hasFocus()" />
                                <span class="err-msg" v-if="errors.exp_month">{{ errors.exp_month }}</span>

                            </div>
                        </div>

                        <div class="skill-label">
                            I have worked in the following areas of construction
                        </div>
                        <div class="bl-inline" v-for="(sector, index) in sectors_list" :key="index">
                            <input :id="'sector-styled-checkbox-'+index" class="styled-checkbox" type="checkbox"
                                :value="sector.id" v-model="input.sectors"
                                @focus="hasFocus()" />

                            <label :for="'sector-styled-checkbox-'+index" style="width:125px">{{ sector.business_type }}</label>
                        </div>

                        <div class="skill-label">
                            In the following Tiers
                        </div>
                        <div class="bl-inline" v-for="(tier, index) in tiers_list" :key="index + sectors_list.length">
                            <input :id="'tier-styled-checkbox-'+index" class="styled-checkbox" type="checkbox"
                                :value="tier.id" v-model="input.tiers"
                                @focus="hasFocus()" />

                            <label :for="'tier-styled-checkbox-'+index">{{ tier.tier_name }}</label>
                        </div>
                    </form>
                </template>

                <template slot="custom-modal-footer">
                    <button class="pull-right" type="submit" @click="submit" :disabled="disabled">
                        Save Changes
                    </button>
                </template>

            </main-modal>

            <div @click="open">
                <edit-icon cls="edit-icon edit-icon-2" data-target="#modalUserProfile"></edit-icon>
            </div>

            <input type="file" id="upload" value="Choose a file" accept="image/*" style="display:none" @change="onFileChange" />

            <div class="profile-header" >
                <img v-if="profile_photo_url" class="profile-picture" :src="profile_photo_url" alt="" @click="onClickProfilePhoto">
                <div v-else @click="onClickProfilePhoto">
                    <avatar cls="profile-picture" size="110" border="0"></avatar>
                </div>

                <div class="chat-button-wrapper" v-if="editable === false && internalRole == 2 && request_chat_button.disabled != true">
                    <a :href="request_chat_button.link"
                       :class="'btn btn-'+request_chat_button.type"
                       style="width: 100%; margin-top: 20px; border-radius: 0;"
                        @click="requestChat"
                        :data-user_id="user_id">{{request_chat_button.text}}</a>
                </div>
            </div>
            <div class="profile-content-p20 pb-4">
                <div class="bl-label-22 m0">{{ first_name }} {{ last_name.substr(0, 1) + '.' }}</div>

                <div class="bl-label-17 pb-3" v-if="job_role">
                    {{ job_role }} <div class="text-style-1">- {{ company_name }}</div>
                </div>

                <div class="row bl-label-15" v-if="address">
                    <div class="bl-col-3">
                        <img class="text-icon-3" src="/img/icons/pinlocation.png"
                            srcset="/img/icons/pinlocation@2x.png 2x, /img/icons/pinlocation@3x.png 3x">
                    </div>
                    <div class="bl-col-4 bl-display">
                        {{ address }}
                    </div>
                </div>

                <div class="row bl-label-15" v-if="education_id">
                    <div class="bl-col-3">
                        <img class="text-icon-4" src="/img/icons/smalleducation.png"
                            srcset="/img/icons/smalleducation@2x.png 2x, /img/icons/smalleducation@3x.png 3x">
                    </div>
                    <div class="bl-col-4 bl-display">
                        Studied <b>{{ course }}</b> <div class="text-style-1">- {{ school }}</div>
                    </div>
                </div>

                <div class="row bl-label-15" v-if="sectors.length > 0 || tiers.length > 0">
                    <div class="bl-col-3">
                        <img class="text-icon-5" src="/img/icons/industrysegment.png"
                            srcset="/img/icons/industrysegment@2x.png 2x, /img/icons/industrysegment@3x.png 3x">
                    </div>
                    <div class="bl-col-4">
                        <div class="bl-display" v-for="(sector, si) in sectors" :key="si">
                            {{ sector.business_type }}
                            <div class="bl-inline" v-if="si != sectors.length-1"> •&nbsp;</div>
                        </div>
                    </div>
                </div>

                <div class="row bl-label-15" style="margin-left:34px;color:#6b7172" v-if="tiers && tiers.length > 0">
                    <div class="bl-col-4">
                        <div class="bl-display" v-for="(tier, ti) in tiers" :key="ti">
                            {{ tier.tier_name }}
                            <div class="bl-inline" v-if="ti != tiers.length-1"> •&nbsp;</div>
                        </div>
                    </div>
                </div>

                <div class="bl-display">
                    <span class="profile-role-header mb-0" style="margin-top: 10px;">Career Summary</span>
                    <div ref="description" class="bl-label-15 description-wrapper">
                        {{ profile_description }}
                        <span ref="descriptionBackground" class="opaque-background"></span>
                    </div>
                    <div ref="descriptionExpander" style="margin-bottom: 20px;" v-show="career_summary_length > 65">
                        <span @click="descriptionExpand()">{{description_expand_text}}</span>
                    </div>
                </div>

                <span class="profile-role-header mb-0" v-if="most_recent_role">Most Recent Role</span>

                <div v-if="most_recent_role">
                    <div class="bl-label-15">
                        {{ most_recent_role }}
                    </div>
                </div>

                <div class="bl-display" v-if="exp_year || exp_month">
                    <div class="bl-label-14">
                        {{ formatYearsExperience() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import Avatar from '../common/Avatar';
    import MainModal from '../common/MainModal';
    import PhotoModal from '../common/PhotoModal';
    import EditIcon from '../common/EditIcon';
    import Alert from '../common/Alert';

    export default {
        name: "user-profile",
        props: {
            internalRole: {
                type: String,
                required: false
            },
        },
        data() {
            return {
                user_id: '',
                own_profile: false,
                request_chat_button: {
                    text: '', link: '', disabled: '', type: ''
                },
                has_focus_role: false,
                has_focus_location: false,
                editable: false,
                disabled: false,
                time_out: false,
                educations: [],
                locations: [],
                job_roles: [],
                profile_photo_url: '',
                profile_description: '',
                first_name: '',
                last_name: '',
                email: '',
                is_verified: '',
                sectors: [],
                tiers: [],
                company_name: '',
                job_role: '',
                most_recent_role: '',
                exp_year: '',
                exp_month: '',
                address: '',
                education_id: '',
                course: '',
                school: '',
                description_expand_text: 'View More',
                career_summary_length: null,
                sectors_list: [
                    { id: 1, business_type: 'Residential' },
                    { id: 2, business_type: 'Commercial' },
                    { id: 3, business_type: 'Civil' },
                    { id: 4, business_type: 'Industrial' }
                ],
                tiers_list: [
                    { id: 1, tier_name: 'Tier 1' },
                    { id: 2, tier_name: 'Tier 2' },
                    { id: 3, tier_name: 'Tier 3' },
                    { id: 4, tier_name: 'Tier 4' },
                ],
                input: {
                    profile_description: '', first_name: '', last_name: '', address: '', education_id: '',
                    most_recent_role: '', exp_year: '', exp_month: '', tiers: [], sectors: [],
                },
                errors: {
                    profile_description: '', first_name: '', last_name: '', address: '', education_id: '',
                    most_recent_role: '', exp_year: '', exp_month: '', tiers: '', sectors: '',
                },
                endpoints: {
                    save: '/api/v1/worker/introduction',
                    educations: '/api/v1/worker/educations',
                },
            }
        },
        created() {
            let vm = this;

            this.editable = Api.showingOwnProfile();
            Bus.$on('userProfileDetails', function(details) {

                vm.setValues(details);
                vm.setDisplayValues(vm.input, details);
                // vm.doesRequestExist(details);
                vm.checkIfApplicationExists(details);
                vm.profileDescriptionLength();

                if (! vm.is_verified) {
                    Bus.$emit('alertVerify', vm.email);
                }
            });

            Bus.$on('removeEducation', function(index, id) {
                if (vm.education_id == id) {
                    vm.course = '';
                    vm.school = '';
                    vm.education_id = '';
                }
            });

            Bus.$on('updateEmployment', function(index, details) {

                if (index == 0 || (!vm.company_name && index == -1)) {
                    vm.company_name = (details.company) ? details.company.name : details.company_name;
                    vm.job_role = (details.job) ? details.job.title : details.job_role;
                }
            });

            Bus.$on('addAnotherEmployment', function() {

                setTimeout(() => {
                    $('#modalEmployment').modal('show');
                    Bus.$emit('showEmployment', -1, null);
                }, 700)
            });

            Bus.$on('removeEmployment', function(index) {
                if (index == 0) {
                    vm.company_name = '';
                    vm.job_role = '';
                }
            });

            Bus.$emit('activateTab', 'profile');

            // Bus.$on('croppedPhoto', function(photo_url) {
            //     vm.profile_photo_url = photo_url;
            // });

            Bus.$on('closePhotoModal', function() {
                $('#upload').val('');
            });

            this.editable = Api.checkAuthUser();
        },
        methods: {
            checkIfApplicationExists(details) {
                let vm = this;
                axios.get('/api/v1/job/applicants/'+details.user_id ,Utils.getBearerAuth())
                    .then(function(response) {
                        console.log(response.data.data);
                        vm.buildRequestButton(vm, response.data.data, details.user_id);
                    });
            },
            requestChat(e) {

                let vm = this;
                let messageObj = {
                    'requested_user': e.target.getAttribute('data-user_id')
                };

                Api.submit('/api/v1/chat/create-chat-request', messageObj, 'requestChatSuccess');

                Bus.$on('requestChatSuccess', function() {
                    window.location = '/messages/' +e.target.getAttribute('data-user_id');
                });
            },
            buildRequestButton(vm, data, userId)
            {
                vm.request_chat_button.link = '#';
                vm.request_chat_button.disabled = false;
                vm.request_chat_button.type = 'primary';

                if(data > 0 ) {
                    vm.request_chat_button.text = 'Open Chat';
                    vm.request_chat_button.link = '/messages/'+userId;
                } else {
                    vm.request_chat_button.disabled = true;
                }

                console.log(vm.request_chat_button);
            },
            hasFocus() {
                this.has_focus_role = false;
                this.has_focus_location = false;
            },
            hasFocusRole(has_focus) {
                this.has_focus_role = has_focus;

                if (has_focus) {
                    this.has_focus_location = false;
                }
            },
            hasFocusLocation(has_focus) {
                this.has_focus_location = has_focus;

                if (has_focus) {
                    this.has_focus_role = false;
                }
            },
            setValues(details) {
                this.user_id = details.user_id;
                this.profile_description = details.profile_description;
                this.profile_photo_url = details.profile_photo_url;
                this.first_name = details.first_name;
                this.last_name = details.last_name;
                this.email = details.email;
                this.is_verified = details.is_verified;
                this.sectors = details.sectors;
                this.tiers = details.tiers;
                this.company_name = details.company_name;
                this.job_role = details.job_role;
                this.most_recent_role = details.most_recent_role;
                this.exp_year = details.exp_year;
                this.exp_month = details.exp_month;
                this.address = details.address;
                this.education_id = details.education_id;
                this.course = details.education ? (details.education.course ? details.education.course.course_name: details.education.course_name) : '';
                this.school = details.education ? details.education.school : '';
            },
            setDisplayValues(val, details) {
                val.user_id = details.user_id;
                val.profile_description = details.profile_description;
                val.first_name = details.first_name;
                val.last_name = details.last_name;
                val.most_recent_role = details.most_recent_role;
                val.exp_year = details.exp_year;
                val.exp_month = details.exp_month;
                val.address = details.address;
                val.education_id = details.education_id;
                val.course = details.education ? (details.education.course ? details.education.course.course_name: details.education.course_name) : '';
                val.school = details.education ? details.education.school : '';

                for (let i = 0; i < details.sectors.length; i++) {
                    val.sectors.push(details.sectors[i].id);
                }

                for (let i = 0; i < details.tiers.length; i++) {
                    val.tiers.push(details.tiers[i].id);
                }
            },
            formatYearsExperience() {
                return (this.exp_year ? (this.exp_year + (this.exp_year > 1 ? ' years' : ' year')) : '') +
                        ((this.exp_year && this.exp_month) ? ' and ' : '') +
                        (this.exp_month ? (this.exp_month + (this.exp_month > 1 ? ' months' : ' month')) : '');
            },
            onClickProfilePhoto() {
                if (this.editable) {
                    upload.click();
                }
            },
            onFileChange(e) {
                Utils.onFileChange(e, 0, 'User');
            },
            textAreaAdjust(refName) {
                this.hasFocus();

                Utils.textAreaAdjust(this.$refs[refName]);
            },
            open() {
                this.loadEducations();
            },
            close() {
                this.setDisplayValues(this.input, this);
            },
            loadEducations() {
                let vm = this;

                this.educations = [];

                axios.get(vm.endpoints.educations, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    vm.educations = data.data.educations;

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },
            onChangeLocation(keyword) {

                if (keyword && keyword.length > 0) {

                    Promise.resolve(Api.getLocationsPromise(keyword)).then((data) => {

                        this.locations = (data.data && data.data.locations) ? data.data.locations.features : [];

                    });

                } else {

                    this.locations = [];
                }
            },
            onSelectLocation(location) {
                this.input.address = location;

                this.locations = [];
            },
            onSearchJob(keyword) {
                this.job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];
            },
            onSelectJob(job) {
                this.input.most_recent_role = job.job_role_name;

                this.job_roles = [];
            },
            async submit() {
                let vm = this;

                Utils.setObjectValues(this.errors, '');

                this.disabled = true;

                Api.submit('/api/v1/worker/sectors', { sectors: this.input.sectors, tiers: this.input.tiers });

                await axios.post(vm.endpoints.save, vm.$data.input, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    $('#modalUserProfile').modal('hide');

                    vm.setDisplayValues(vm, data.data.introduction);

                    vm.sectors = [];
                    vm.tiers = [];

                    for (let i = 0; i < vm.input.sectors.length; i++) {
                        vm.sectors.push(vm.sectors_list.find(obj => obj['id'] == vm.input.sectors[i]));
                    }

                    for (let i = 0; i < vm.input.tiers.length; i++) {
                        vm.tiers.push(vm.tiers_list.find(obj => obj['id'] == vm.input.tiers[i]));
                    }

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                this.disabled = false;
            },
            profileDescriptionLength()
            {
                this.career_summary_length = this.profile_description.length;
            },
            descriptionExpand()
            {
                let text = this.description_expand_text;

                switch(text) {
                    case 'View More':
                        this.description_expand_text = 'View Less';
                        this.$refs.description.style.height = 'auto';
                        this.$refs.descriptionBackground.style.display = 'none';
                        break;
                    case 'View Less':
                        this.description_expand_text = 'View More';
                        this.$refs.description.style.height = '57px';
                        this.$refs.descriptionBackground.style.display = 'block';
                        break;
                    default:
                        this.description_expand_text = 'View More';
                        this.$refs.description.style.height = '57px';
                        this.$refs.descriptionBackground.style.display = 'block';
                }

            }
        },
        components: {
            Avatar,
            MainModal,
            PhotoModal,
            EditIcon,
            Alert
        },
    }
</script>
<style>
    .description-wrapper {
        height: 57px; overflow: hidden; transition: all linear 0.25s;
        position: relative;
    }

    .description-wrapper .opaque-background {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 17px;
        background: linear-gradient(transparent, #fff);
    }
</style>
