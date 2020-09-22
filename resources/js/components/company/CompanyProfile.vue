<template>
    <div class="profile-item-1">
        <div class="profile-content">

            <photo-modal></photo-modal>

            <main-modal id="modalCompanyProfile">

                <template slot="custom-modal-title">
                    <h4 class="modal-title">Edit Introduction</h4>
                    <div class="close" data-dismiss="modal" @click="close">&times;</div>
                </template>

                <template slot="custom-modal-content">
                    <form class="modal-form" method="POST" @submit.prevent="submit">
                        <div class="skill-label">Company Overview</div>
                        <textarea rows="4" ref="companyIntro" class="form-control" style="overflow:hidden"
                            placeholder="Example: We are a modern, professional and sophisticated surveying firm specialising in land development, construction and engineering surveying. We provide quality, cost-effective and efficient surveying service."
                            @focus="textAreaAdjust('companyIntro')"
                            @keyup="textAreaAdjust('companyIntro')"
                            v-model="input.introduction">
                        </textarea>

                        <span class="err-msg" v-if="errors.introduction">
                            {{ errors.introduction }}
                        </span>

                        <div class="emp-row">
                            <div class="modal-form-label">Company Name</div>
                            <input id="name" type="text" name="name" class="form-control" style="padding-left:24px"
                                @focus="hasFocus()"
                                v-model="input.name" required />

                            <span class="err-msg" v-if="errors.name">
                                {{ errors.name }}
                            </span>
                        </div>

                        <div class="emp-row">
                            <div class="modal-form-label">Location</div>
                            <input class="form-control" type="text" v-model="input.address"
                                @focus="hasFocusLocation(true)"
                                @keyup="onChangeLocation(input.address)" />

                            <span class="err-msg" v-if="errors.address">
                                {{ errors.address }}
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
                            <div class="modal-form-label">Website</div>
                            <input class="form-control" type="text" v-model="input.website"
                                @focus="hasFocus()" />

                            <span class="err-msg" v-if="errors.website">
                                {{ errors.website }}
                            </span>
                        </div>


                        <div class="form-group toggle-select-wrapper"><div class="modal-form-label phone-label">Contact Number</div>
                            <div class="input-toggle">
                                <input type="text" value="" class="form-control" name="text" maxlength="10" v-model="input.phone" placeholder="Business contact number" @focus="hasFocus()" @keyup="focusOut"/>
                                <div class="btn-toogle-wrapper">
                                    <button
                                        type="button" class="btn btn-default dropdown-toggle as-is bs-dropdown-to-select" data-toggle="dropdown">
                                        <span data-bind="bs-drp-sel-label">{{ phoneType }}</span>
                                        <input type="hidden" name="selected_value" data-bind="bs-drp-sel-value" value=""/>
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu" role="menu" style="">
                                        <div>
                                            <a href="javascript:void(0)" @click="onSelectPhoneType('Mobile')">Mobile</a>
                                        </div>
                                        <div>
                                            <a href="javascript:void(0)" @click="onSelectPhoneType('Landline')">Landline</a>
                                        </div>
                                    </ul>
                                </div>
                            </div>
                            <span class="err-msg" v-if="errors.phone">
                                {{ errors.phone }}
                            </span>
                        </div>

                        <div class="emp-row">
                            <div class="modal-form-label">Email</div>
                            <input class="form-control" type="text" v-model="input.email"
                                   @focus="hasFocus()" />
                        </div>

                        <div class="skill-label" style="margin-bottom:-15px">Company Details</div>

                        <div class="emp-row">
                            <div class="modal-form-label">Industry Segment Type</div>
                            <select v-model="input.business_type.id" @focus="hasFocus()">
                                <option v-for="(type, index) in business_types" :key="index" v-bind:value="type.id">
                                    {{ type.business_type }}
                                </option>
                            </select>
                        </div>
                        <span class="err-msg" v-if="errors.business_type">
                            {{ errors.business_type }}
                        </span>

                        <div class="emp-row">
                            <div class="modal-form-label">Market Segment</div>
                            <select v-model="input.tier.id" @focus="hasFocus()">
                                <option v-for="(tier, index) in tiers" :key="index" v-bind:value="tier.id">
                                    {{ tier.tier_name }}
                                </option>
                            </select>
                        </div>
                        <span class="err-msg" v-if="errors.tier">
                            {{ errors.tier }}
                        </span>

                        <div class="comp-label">
                            What is your main company function?
                        </div>
                        <div class="emp-row">
                            <select v-model="input.main_function.id" @change="onChangeMainCompanyFunctions"
                                @focus="hasFocus()">

                                <option value="" disabled selected style="display:none">Company Specialisation</option>
                                <option v-for="(main, index) in main_functions" :key="index" v-bind:value="main.id">
                                    {{ main.main_name }}
                                </option>
                            </select>
                        </div>

                        <div class="comp-label" v-if="input.main_function.id">
                            {{ specialtyLabel }}
                        </div>

                        <div class="emp-row mt-4" v-if="input.main_function.id && input.main_function.id != 1">
                            <input class="form-control" type="text" placeholder="Start typing..."
                                v-model="input.main_function_answer"
                                @focus="hasFocusAnswer(true)"
                                @keyup="onSearchMainFunctionAnswer(input.main_function_answer, input.main_function.id)" />

                            <span class="err-msg" v-if="errors.main_function_answer">
                                {{ errors.main_function_answer }}
                            </span>
                        </div>

                        <div class="emp-row" style="margin-top:0" v-if="has_focus_answer && main_function_answers && main_function_answers.length > 0">
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(ans, idx) in main_function_answers" :key="idx"
                                    @click="onSelectMainFunctionAnswer(ans.answer)">

                                    {{ ans.answer }}
                                </li>
                            </ul>
                        </div>

                        <!-- <div class="comp-label">
                            What are the secondary functions?
                        </div>
                        <div class="comp-label-3">
                            Add as many as applicable
                        </div>

                        <div class="form-group emp-row row-center"
                            :ref="'specItem-' + index"
                            v-for="(spec, index) in input.secondary_functions"
                            :key="index">

                            <div class="comp-col-left">
                                <select v-model="spec.id">
                                    <option value="" disabled selected style="display:none">Company Specialisation</option>
                                    <option v-for="(type, idx) in specialization"
                                        :key="idx + 1"
                                        v-bind:value="type.id">

                                        {{ type.secondary_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="comp-col-right">
                                <span @click="removeEntity(index)">
                                    <img src="/img/icons/remove.png"
                                        srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x"
                                        style="cursor:pointer">
                                </span>
                            </div>
                        </div>

                        <center>
                            <div class="btn btn-link btn-delete" @click="addNewEntity">
                                Add Another
                            </div>
                        </center> -->

                    </form>
                </template>

                <template slot="custom-modal-footer">
                    <button class="pull-right" type="submit" @click="submit" :disabled="disabled">
                        Save Changes
                    </button>
                </template>

            </main-modal>

            <span v-if="editable"
                class="edit-icon edit-icon-2"
                data-toggle="modal"
                data-backdrop="static"
                data-keyboard="false"
                data-target="#modalCompanyProfile"
                @click="open">

                <img src="/img/icons/editbutton.png"
                    srcset="/img/icons/editbutton@2x.png 2x, /img/icons/editbutton@3x.png 3x">
            </span>

            <input type="file" id="upload" value="Choose a file" accept="image/*" style="display:none" @change="onFileChange" />

            <div class="profile-header" >
                <div class="company-image" @click="onClickProfilePhoto">
                    <img v-if="photo_url" :src="photo_url">
                    <avatar v-else cls="" size="110" border="0" border-radius="8px"></avatar>
                </div>
            </div>
            <div class="profile-content-p20 pb-4">
                <div class="bl-label-22 m0">{{ name }}</div>

                <div class="bl-label-17 pb-3" v-if="main_function">
                    {{ main_function.main_name }}
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

                <div class="row bl-label-15" v-if="website">
                    <div class="bl-col-3">
                        <img class="text-icon-5" src="/img/icons/globe.png"
                            srcset="/img/icons/globe@2x.png 2x, /img/icons/globe@3x.png 3x">
                    </div>
                    <div class="bl-col-4 bl-display">
                        <a target="_blank" :href="website">{{ website }}</a>
                    </div>
                </div>

                <div class="row bl-label-15"  v-if="phone">
                    <div class="bl-col-3">
                        <img class="text-icon-5" src="/img/icons/phone.png"
                            srcset="/img/icons/phone@2x.png 2x, /img/icons/phone@3x.png 3x">
                    </div>
                    <div class="bl-col-4 bl-display">
                        {{ phone }}
                    </div>
                </div>

                <div class="row bl-label-15"  v-if="business_type.business_type || tier.tier_name">
                    <div class="bl-col-3">
                        <img class="text-icon-5" src="/img/icons/industrysegment.png"
                            srcset="/img/icons/industrysegment@2x.png 2x, /img/icons/industrysegment@3x.png 3x">
                    </div>
                    <div class="bl-col-4 bl-display">
                        {{ business_type.business_type }} • {{ tier.tier_name }}
                    </div>
                </div>

                <div class="bl-display" v-if="introduction">
                    <div class="bl-label-15-style-2" style="margin-top:24px">
                        {{ introduction }}
                    </div>
                </div>
                <div class="bl-label-16 bl-mt20" v-if="main_function_answer">
                    We specialise in
                </div>
                <div class="job-body">
                    {{ main_function_answer }}
                </div>
                <!-- <div class="bl-label-16 bl-mt20" v-if="secondary_functions.length != 0">
                    We specialise in
                </div>
                <div class="job-body">
                    <ul class="job-list-items">
                        <li v-for="(spec, index) in secondary_functions" :key="index">
                            {{ spec.secondary_name }}
                        </li>
                    </ul>
                </div> -->
            </div>
        </div>

        <button style="width:100%" :disabled="disabled" v-if="showButton" @click="postNewJob(0)">
            Post New Job
        </button>
    </div>
</template>

<script>
    import Api from '@/api';
    import Avatar from '../common/Avatar';
    import MainModal from '../common/MainModal';
    import PhotoModal from '../common/PhotoModal';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "company-profile",
        data() {
            return {
                has_focus_location: false,
                has_focus_answer: false,
                editable: false,
                disabled: false,
                time_out: false,
                locations: [],
                photo_url: '',
                name: '',
                business_type: '',
                tier: '',
                address: '',
                website: '',
                phone: '',
                introduction: '',
                main_function: '',
                main_function_answer: '',
                business_types: [],
                tiers: [],
                main_functions: [],
                secondary_functions: [],
                specialization: [],
                main_function_answers: [],
                specialtyLabel: '',
                specialtyLabels: [
                    'What is your trade?',
                    'What do you supply?',
                    'What type of design?',
                    'What type of education provider are you?'
                ],
                email: '',
                input: {
                    name: '', address: '', website: '', phone: '', introduction: '',
                    business_type: { id: 0, business_type: '' },
                    tier: { id: 0, tier_name: '' },
                    main_function: { id: 0, main_name: '' },
                    business_type_id: '', tier_id: '', main_company_id: '',
                    secondary_functions: [], main_function_answer: '', email: ''
                },
                errors: {
                    name: '', business_type: '', tier: '', address: '', website: '', phone: '', introduction: '',
                    main_function: '', secondary_functions: '', main_function_answer: '',
                },
                endpoints: {
                    save: '/api/v1/company/update',
                    secondary_options: '/api/v1/company/options/',
                },
                phoneType: 'Mobile',
                phonePlaceholder: 'XXXX XXX XXX',
                phoneValue: 0,
                formattedPhoneValue: "0",
            }
        },
        created() {
            let vm = this;

            Bus.$on('companyProfileDetails', function(details) {
                console.log(details);
                vm.setValues(details);
                vm.setDisplayValues(vm.input, details);
            });

            Bus.$emit('activateTab', 'profile');

            this.getCompanyOptions();

            this.editable = Api.checkAuthUser();
        },
        props: {
            companyId: {
                type: String,
                required: false
            },
        },
        computed: {
            showButton() {
                return location.pathname === '/user/profile';
            },
        },
        methods: {
            hasFocus() {
                this.has_focus_location = false;
                this.has_focus_answer = false
            },
            hasFocusAnswer(has_focus) {
                this.has_focus_answer = has_focus;

                if (has_focus) {
                    this.has_focus_location = false;
                }
            },
            hasFocusLocation(has_focus) {
                this.has_focus_location = has_focus;

                if (has_focus) {
                    this.has_focus_answer = false;
                }
            },
            getCompanyOptions() {
                let vm = this;

                Promise.resolve(Api.getCompanyOptions()).then(function(data) {
                    vm.business_types = data.business_types;
                    vm.tiers = data.tiers;
                    vm.main_functions = data.main_company_functions;
                });
            },
            setValues(details) {
                this.photo_url = details.photo_url;
                this.name = details.name;
                this.address = details.address;
                this.website = details.website;
                this.phone = details.phone;
                this.introduction = details.introduction;
                this.business_type = details.business_type ? details.business_type : { id: 0, business_type: '' };
                this.tier = details.tier ? details.tier : { id: 0, tier_name: '' };
                this.main_function = details.main_function ? details.main_function : { id: 0, main_name: '' };
                this.secondary_functions = details.specialization;
                this.main_function_answer = details.main_function_answer;
                this.email = details.user_email;

                this.getSecondaryOptions(details.main_function.id);
                this.specialtyLabel = this.specialtyLabels[details.main_function.id - 2];
            },
            setDisplayValues(val, details) {
                val.name = details.name;
                val.address = details.address;
                val.website = details.website;
                val.phone = details.phone;
                val.introduction = details.introduction;
                val.business_type = details.business_type ? details.business_type : { id: 0, business_type: '' };
                val.tier = details.tier ? details.tier : { id: 0, tier_name: '' };
                val.main_function = details.main_function ? details.main_function : { id: 0, main_name: '' };
                val.secondary_functions = details.specialization;
                val.main_function_answer = details.main_function_answer;
                val.email = details.user_email;
            },
            getSecondaryOptions(id) {
                let vm = this;

                axios.get(vm.endpoints.secondary_options + id, Utils.getBearerAuth())

                .then(function(response) {

                    vm.specialization = response.data.data;

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },
            textAreaAdjust(refName) {
                this.hasFocus();

                Utils.textAreaAdjust(this.$refs[refName]);
            },
            open() {

            },
            close() {
                this.setDisplayValues(this.input, this);
            },
            onClickProfilePhoto() {
                if (this.editable) {
                    upload.click();
                }
            },
            onFileChange(e) {
                Utils.onFileChange(e, 0, 'Company');
            },
            onChangeMainCompanyFunctions(e) {
                this.input.secondary_functions = [];

                this.getSecondaryOptions(e.target.value);
                this.specialtyLabel = this.specialtyLabels[e.target.value - 2];
            },
            onChangeLocation(keyword) {
                this.locations = (keyword && keyword.length > 0) ? Api.getLocations(keyword) : [];
            },
            onSelectLocation(location) {
                this.input.address = location;

                this.locations = [];
            },
            onSearchMainFunctionAnswer(keyword, main_id) {
                this.main_function_answers = (keyword && keyword.length > 0) ? Api.getMainFunctionAnswers(keyword, main_id) : [];
            },
            onSelectMainFunctionAnswer(answer) {
                this.input.main_function_answer = answer;

                this.main_function_answers = [];
            },
            async submit() {
                let vm = this;
                let espArray = [];
                let temp = vm.input.secondary_functions;

                Utils.setObjectValues(this.errors, '');

                this.disabled = true;

                for (let i = 0; i < temp.length; i++) {
                    espArray.push(temp[i].id);
                }

                vm.input.secondary_functions = espArray;
                vm.input.business_type_id = vm.input.business_type.id;
                vm.input.tier_id = vm.input.tier.id;
                vm.input.main_company_id = vm.input.main_function.id;

                await axios.post(vm.endpoints.save, vm.$data.input, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    console.log(response.data);

                    $('#modalCompanyProfile').modal('hide');

                    vm.setValues(data.data.company);
                    vm.setDisplayValues(vm.input, data.data.company);

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;

                    vm.input.secondary_functions = temp;
                });

                this.disabled = false;
            },
            postNewJob() {
                window.location.href = '/job/list?type=templates';
            },
            focusOut() {

                // this.input.phone = this.input.phone.replace(/[^0-9]/g, '');

                this.input.phone = this.phoneType === 'Mobile' ? this.input.phone.replace(/(\d{10})/, '$1') : this.input.phone.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');

                // this.input.phone = this.input.phone.substr(0, 11).replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '');

            },
            onSelectPhoneType(type) {

                this.phoneType = type;

                this.focusOut()
            },
        },
        components: {
            Avatar,
            MainModal,
            PhotoModal,
            EditIcon,
        },
    }
</script>

<style scoped>
.edit-icon img{
    width: unset !important;
    height: unset !important;
}
.phone-label{
    position: relative;
    width: 100px;
}
</style>
