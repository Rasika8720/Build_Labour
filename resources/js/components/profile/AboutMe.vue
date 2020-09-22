<template>
    <div class="profile-item-1">
        <div class="profile-content">
            <div class="profile-content-p20 pt-3 pb-4">

                <main-modal id="modalAboutMe">

                    <template slot="custom-modal-title">
                        <h4 class="modal-title">Edit About Me</h4>
                        <div class="close" data-dismiss="modal" @click="close">&times;</div>
                    </template>

                    <template slot="custom-modal-content">

                        <personal-details></personal-details>

                        <div class="mt-4"></div>

                        <work-information></work-information>

                    </template>

                    <template slot="custom-modal-footer">
                        <div class="btn btn-link btn-delete" data-dismiss="modal" @click="deleteRecord"
                            v-if="gender || has_tfn || has_abn || english_skill || drivers_license || has_registered_vehicle">
                            Delete
                        </div>

                        <button class="pull-right" type="submit" @click="submit" :disabled="disabled">
                            Save Changes
                        </button>
                    </template>

                </main-modal>

                <edit-icon cls="edit-icon" data-target="#modalAboutMe"></edit-icon>

                <div class="profile-title">About Me</div>

                <span class="bl-label-15 mt-2 pt-1" v-if="gender">Gender</span>
                <span class="bl-label-14">
                    {{ gender }}
                </span>

                <div v-if="editable">
                    <span class="bl-label-15 mt-2 pt-1" v-if="date_of_birth">Date of Birth</span>
                    <span class="bl-label-14">
                        {{ formatDate(date_of_birth) }}
                    </span>

                    <!-- <span class="bl-label-15 mt-2 pt-1" v-if="country_birth">Country of Birth</span>
                    <span class="bl-label-14">
                        {{ country_birth }}
                    </span> -->
                </div>
                <div v-if="false">
                    <span class="bl-label-15 mt-2 pt-1" v-if="marital_status">Marital Status</span>
                    <span class="bl-label-14">
                        {{ marital_status }}
                    </span>
                </div>

                <span class="bl-label-15 mt-2 pt-1" v-if="english_skill">English Skill</span>
                <span class="bl-label-14">
                    {{ english_skill }}
                </span>

                <span class="bl-label-15 mt-2 pt-1" v-if="drivers_license">Driver's Licence</span>
                <span class="bl-label-14">
                    {{ drivers_license }}
                    <br>{{ can_drive_manual }}
                </span>
                <span class="bl-label-14">
                    {{ has_registered_vehicle }}
                </span>

                <span class="bl-label-15 mt-2 pt-1">Australian ID</span>
                <span class="bl-label-14">
                    {{ has_tfn }}
                </span>
                <span class="bl-label-14">
                    {{ has_abn }}
                </span>

                <span class="bl-label-15 mt-2 pt-1" v-if="right_to_work">Right To Work</span>
                <span class="bl-label-14">
                    {{ right_to_work }}
                </span>

                <span class="bl-label-15 mt-2 pt-1" v-if="has_whitecard">Whitecard</span>
                <span class="bl-label-14" v-if="has_whitecard">
                    {{ has_whitecard }}
                </span>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import PersonalDetails from '../onboarding/PersonalDetails';
    import WorkInformation from '../onboarding/WorkInformation';
    import MainModal from '../common/MainModal';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "about-me",
        data() {
            return {
                editable: false,
                disabled: false,
                days: [],
                months: Utils.getMonths(),
                years: Utils.getYears(new Date().getFullYear() - 18),
                birthDay: '',
                birthMonth: '',
                birthYear: '',
                gender: '',
                date_of_birth: '',
                country_birth: '',
                marital_status: '',
                right_to_work: '',
                has_tfn: '',
                has_abn: '',
                english_skill: '',
                drivers_license: '',
                can_drive_manual: '',
                has_registered_vehicle: '',
                has_whitecard: ''
            }
        },

        created() {
            let vm = this;

            Bus.$on('ticketsDetails', function(detailsArray, detail) {
                if (detail == 1) {
                    vm.has_whitecard = 'Has current White Card';
                } else {
                    vm.has_whitecard = false;
                }
            });

            Bus.$on('aboutMeGeneralDetails', function(details) {
                if (details) {
                    vm.setGeneralValues(vm, details);
                }
            });

            Bus.$on('aboutMeTechnicalDetails', function(details) {
                if (details) {
                    vm.setTechnicalValues(vm, details);
                }
            });

            Bus.$on('removeAboutMe', function() {
                vm.submit('clear');
            });

            this.editable = Api.checkAuthUser();
        },

        methods: {

            setGeneralValues(val, details) {
                val.gender = details.gender ? details.gender : null;
                val.date_of_birth = details.date_of_birth ? details.date_of_birth : null;
                val.country_birth = details.country_birth ? details.country_birth : null;
            },

            setTechnicalValues(val, details) {
                if (! Utils.isNullOrEmpty(details.right_to_work)) {
                    val.right_to_work = details.right_to_work == 1 ?
                        'Yes, I have right to work in Australia' :
                        'No, I don\'t have right to work in Australia';
                } else {
                    val.right_to_work = null;
                }

                val.has_tfn = details.australian_tfn && details.australian_tfn.length > 0 ?
                    'Has Australian Tax File Number (TFN)' :
                    'Australian Tax File Number (TFN) Not Supplied';

                if (! Utils.isNullOrEmpty(details.has_abn)) {
                    val.has_abn = details.has_abn == 1 ?
                        'Has Australian Business Number (ABN)' :
                        'Does not have Australian Business Number (ABN)';
                } else {
                    val.has_abn = null;
                }

                if (! Utils.isNullOrEmpty(details.english_skill)) {
                    val.english_skill = details.english_skill == 1 ?
                        'Competent in written and spoken' :
                        'Not competent in written and spoken';
                } else {
                    val.english_skill = null;
                }

                val.drivers_license = details.drivers_license_state !='none' || details.drivers_license_type != 'none' ?
                    'Owns valid licence' :
                    'Does not own valid licence';

                val.can_drive_manual = details.can_drive_manual ? 'Can legally drive manual' : 'Can\'t legally drive manual';

                if (! Utils.isNullOrEmpty(details.has_registered_vehicle)) {
                    val.has_registered_vehicle = details.has_registered_vehicle == 1 ?
                        'Owns/has access to personal registered vehicle' :
                        'Does not own/have access to personal registered vehicle';
                } else {
                    val.has_registered_vehicle = null;
                }
            },

            formatDate(d) {
                if (d) {
                    let date = new Date(d);

                    if (date != 'Invalid Date') {
                        return date.getDate() + ' ' + Utils.getMonth(date.getMonth()) + ' ' + date.getFullYear();
                    }
                }

                this.date_of_birth = null;
            },

            close() {

            },

            deleteRecord() {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteAboutMe');
            },

            submit(action = 0) {

                Bus.$emit('onboardingSubmitWorkInformation', action);

                Bus.$emit('onboardingSubmitPersonalDetails', action);
            },
        },
        components: {
            MainModal,
            PersonalDetails,
            WorkInformation,
            EditIcon,
        },
    }
</script>
