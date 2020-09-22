<template>
    <div class="form-group view-company" v-if="record">
        <div class="record-title">
            {{ record.name }}
        </div>
        <div class="record-photo">
            <div class="record-profile-pic">
                <img v-if="record.photo_url" :src="record.photo_url" height="120px" />
                <img v-else src="/img/logo/1.jpg" height="120px" />
            </div>
        </div>

        <!-- <div class="row">
            <div class="col-md-12">
                <label class="record-label">
                    Workers
                </label>
                <ul>
                    <li v-for="(value, index) in record.workers" :key=index>
                        <span>
                            <img v-if="value.profile_photo_url" :src="value.profile_photo_url" height="40px" />
                            <img v-else src="/img/defaults/user.png" height="40px" />
                            {{ getProfileName(value) }}
                        </span>
                    </li>
                </ul>
            </div>
        </div> -->
        <!-- <div class="row" v-for="(value, key) in record" :key="key.id">
            <div class="col-md-12" v-if="excludes.indexOf(key) < 0">
                <label class="record-label">
                    {{ key.split('_').join(' ').toUpperCase() }}
                </label>
            </div>

            <div class="col-md-12" v-if="key && excludes.indexOf(key) < 0">
                <span v-if="key == 'created_at' || key == 'updated_at' || key == 'deleted_at'"
                    class="form-control record-input">{{ formatDate(value) }}</span>

                <span v-else class="form-control record-input">{{ value }}</span>

            </div>
        </div> -->
        <div class="skill-label" style="margin-bottom:-15px">Company Overview</div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    INTRODUCTION
                </label>
            </div>

            <div class="col-md-12" >
                <textarea class="record-textarea" style="height:180px;border:1px solid #ced4da;" v-model="record.introduction"></textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    COMPANY NAME
                </label>
            </div>

            <div class="col-md-12" >
                <input type="text" class="form-control record-input" v-model="record.name" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    ADDRESS
                </label>
            </div>

            <div class="col-md-12" >
                <input type="text" class="form-control record-input" v-model="record.address" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    NO. OF WORKERS
                </label>
            </div>

            <div class="col-md-12" >
                <input type="text" class="form-control record-input" v-model="record.no_of_workers"  readonly/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    WEBSITE
                </label>
            </div>

            <div class="col-md-12" >
                <input type="text" class="form-control record-input" v-model="record.website" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    PHONE
                </label>
            </div>

            <div class="col-md-12" >
                <input type="text" class="form-control record-input" v-model="record.phone" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    EMAIL
                </label>
            </div>

            <div class="col-md-12" >
                <input type="text" class="form-control record-input" v-model="email" />
            </div>
        </div>
        <div class="skill-label" style="margin-bottom:-15px">Company Details</div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    Industry Segment Type
                </label>
            </div>

            <div class="col-md-12" >
                <select style="margin:0" class="form-control record-input" @change="onChange($event)" v-model="record.business_type_id">
                    <option v-for="type in business_types" :key="type.id" :value="type.id">{{ type.business_type }}</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    Market Segment
                </label>
            </div>

            <div class="col-md-12" >
                <select style="margin:0" class="form-control record-input" @change="onChangeTier($event)" v-model="record.tier_id">
                    <option v-for="tier in tiers" :key="tier.id" :value="tier.id">{{ tier.tier_name }}</option>
                </select>
            </div>
        </div>
        <div class="skill-label" style="margin-bottom:-15px">Company Main Function</div>
        <div class="row">
            <div class="col-md-12" >
                <label class="record-label">
                    COMPANY SPECIALISATION
                </label>
            </div>
            <div class="col-md-12">
                <select v-model="main_function.id" @change="onChangeMainCompanyFunctions" class="form-control record-input" style="margin:0"
                    @focus="hasFocus()">

                    <option value="" disabled selected style="display:none">Company Specialisation</option>
                    <option v-for="(main, index) in main_company_functions" :key="index" v-bind:value="main.id">
                        {{ main.main_name }}
                    </option>
                </select>
            </div>
            <div class="col-md-12 comp-label" v-if="main_function.id">
                {{ specialtyLabel }}
            </div>
            <!-- <div class="col-md-12" >
                <select style="margin:0" class="form-control record-input" @change="onChangeTier($event)" v-model="record.main_company_id">
                    <option v-for="specialisation in main_company_functions" :key="specialisation.id" :value="specialisation.id">{{ specialisation.main_name }}</option>
                </select>
            </div> -->
        </div>
        <div class="row comp-label" v-if="main_function.id && main_function.id != 1">
            <div class="col-md-12" >
                <input class="form-control record-input" style="margin:0" type="text" placeholder="Start typing..."
                    v-model="record.main_function_answer"
                    @focus="hasFocusAnswer(true)"
                    @keyup="onSearchMainFunctionAnswer(record.main_function_answer, main_function.id)" />

                    <span class="err-msg" v-if="errors.main_function_answer">
                        {{ errors.main_function_answer }}
                    </span>
            </div>
        </div>
        <div class="row" style="margin-top:0" v-if="has_focus_answer && main_function_answers && main_function_answers.length > 0">
            <div class="col-md-4" >
                <ul>
                    <li class="list-group-item" v-for="(ans, idx) in main_function_answers" :key="idx"
                        @click="onSelectMainFunctionAnswer(ans.answer)">

                        {{ ans.answer }}
                    </li>
                </ul>
            </div>
        </div>
        <!-- <div class="row">

            <div class="col-md-12">
                <label class="record-label">
                    LOCATIONS
                </label>
            </div>

            <div class="col-md-12">
                <span class="form-control record-input" v-for="(loc, index) in record.locations" :key=index>
                    {{ loc }}
                </span>
            </div>
        </div> -->
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
        <DeleteModal></DeleteModal>
    </div>
</template>

<script>
    import DeleteModal from '../common/DeleteModal';
    import Api from '@/api';

    export default {
        name: "view-company",
		data() {
			return {
                has_focus_answer: false,
                disabled: false,
                record_id: 0,
                record: null,
                email: null,
                business_types: [],
                tiers: [],
                main_company_functions: [],
				endpoints: {
					get: ''
				},
                excludes: [
                    'updated_at',
                    'deleted_at',
                    'workers',
                    'created_by',
                    'locations',
                    'photo_url'
                ],
                main_function: { id: 0, main_name: '' },
                input: {
                    name: '', address: '', website: '', phone: '', introduction: '', email: '',
                    business_type: { id: 0, business_type: '' },
                    tier: { id: 0, tier_name: '' },
                    main_function: { id: 0, main_name: '' },
                    business_type_id: '', tier_id: '', main_company_id: '',
                    secondary_functions: [], main_function_answer: '',
                },
                errors: {
                    name: '', business_type: '', tier: '', address: '', website: '', phone: '', introduction: '',
                    main_function: '', secondary_functions: '', main_function_answer: '',
                },
                specialtyLabel: '',
                specialtyLabels: [
                    'What is your trade?',
                    'What do you supply?',
                    'What type of design?',
                    'What type of education provider are you?'
                ],
                main_function_answers: [],

			}
		},
		created() {
			let vm = this;

            Bus.$on('datatableViewCompany', function(id){
                vm.record_id = id;
                vm.endpoints.get = '/api/v1/admin/company/get?id=' + id;
                vm.viewRecord();
            });

            Bus.$on('croppedPhoto', function (profile_photo_url) {
                vm.record.profile_photo_url = profile_photo_url;
            });

            Bus.$on('closePhotoModal', function() {
                $('#upload').val('');
            });

            Bus.$on('removeJob', function() {
                Bus.$emit('adminSaveChanges', vm.record_id);
            });
            this.getCompanyOptions();

		},
		methods: {
		  	viewRecord() {
				let vm = this;

				axios.get(vm.endpoints.get, Utils.getBearerAuth())

                .then(function(response) {
                    console.log(response.data);
                    vm.record = response.data.data.record;
                    vm.email = response.data.data.email.email;
                    vm.main_function.id = vm.record.main_company_id ? vm.record.main_company_id : { id: 0, main_name: '' };
                })
                .catch(function(error) {

                    Utils.handleError(error);
                });
            },
            formatDate(d) {
                if (d != null) {
                    let date = new Date(d);

                    return date.getDate() + ' ' + Utils.getMonth(date.getMonth()) + ' ' + date.getFullYear();
                }
			},
            getProfileName(value) {
                return value.first_name + ' ' + value.last_name;
            },
            onClickDeleteAccount() {
				Bus.$emit('datatableDeleteUser', this.record_id);
            },
            onClickUploadImage() {
                upload.click();
            },
            onFileChange(e) {
                Utils.onFileChange(e, this.record_id);
            },
            onClickDeleteImage() {
                Bus.$emit('deletePhoto', this.record_id);
            },
            deleteRecord() {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteJob', '/api/v1/admin/company/' + this.record_id);
            },
            async submit() {
                let vm = this;

                Utils.setObjectValues(this.errors, '');

                this.disabled = true;

                vm.record.main_company_id = vm.main_function.id;
                let endpoint = '/api/v1/company/admin-update-company';

                await axios.post(endpoint, vm.$data.record, Utils.getBearerAuth())

                .then(function(response) {

                    Bus.$emit('adminSaveChanges', vm.record.id);

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                this.disabled = false;
            },
            getCompanyOptions() {
                let vm = this;

                Promise.resolve(Api.getCompanyOptions()).then(function(data) {
                    vm.business_types = data.business_types;
                    vm.tiers = data.tiers;
                    vm.main_company_functions = data.main_company_functions;
                });
            },
            onChangeMainCompanyFunctions(e) {

                this.specialtyLabel = this.specialtyLabels[e.target.value - 2];
            },
            hasFocus() {

                this.has_focus_answer = false
            },
            hasFocusAnswer(has_focus) {
                this.has_focus_answer = has_focus;
            },
            onSearchMainFunctionAnswer(keyword, main_id) {
                this.main_function_answers = (keyword && keyword.length > 0) ? Api.getMainFunctionAnswers(keyword, main_id) : [];
            },
            onSelectMainFunctionAnswer(answer) {
                this.record.main_function_answer = answer;

                this.main_function_answers = [];
            },
        },
        components: {
            DeleteModal,
        },
	}
</script>
