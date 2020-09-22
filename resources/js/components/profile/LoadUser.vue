<template>
    <div></div>
</template>

<script>

    import Api from '@/api';

    export default {
        name: "load-user",
        data() {
            return {
                avatar: {
                    initials: '', profile_photo_url: '',
                },
                profile: {
                    profile_description: '', profile_photo_url: '', first_name: '', last_name: '', email: '', is_verified: '',
                    company_name: '', job_role: '', sectors: [], tiers: [], address: '', education_id: '',
                    most_recent_role: '', exp_year: '', exp_month: '', user_id: '',
                },
                about_me_general: {
                    gender: '', date_of_birth: '', country_birth: '', marital_status: '',
                },
                about_me_technical: {
                    right_to_work: '', has_tfn: '', has_abn: '', can_drive_manual: '',
                    english_skill: '', drivers_license: '', drivers_license_state: '', drivers_license_type: '', has_registered_vehicle: '',
                },
                ideal_role: {
                    introduction: '', when: '', max_distance: '', state: '', right_to_work: '',
                },
                employments: [],
                educations: [],
                tickets: [],
                industry_skills: [],
                endpoints: {
                    get: '/api/v1/auth/user',
                    get_user: '/api/v1/worker/view/'
                },
            }
        },

        props: {
            userId: {
                type: String,
                required: false
            },
        },

        computed: {
            endpointGet() {
                return this.userId ? this.endpoints.get_user + this.userId : this.endpoints.get;
            }
        },

        created() {
            this.getUser();
        },

        methods: {

            getUser() {
                let vm = this;

                axios.get(vm.endpointGet, Utils.getBearerAuth())

                .then(function(response) {
                    let user = response.data.data.user;

                    vm.profile = {};
                    vm.profile.profile_photo_url = user.profile_photo_url;
                    vm.profile.profile_description = user.worker_detail ? user.worker_detail.profile_description : '';
                    vm.profile.first_name = user.first_name;
                    vm.profile.last_name = user.last_name;
                    vm.profile.email = user.email;
                    vm.profile.is_verified = user.is_verified;
                    vm.profile.address = user.address;
                    vm.profile.education_id = user.worker_detail.education_id;
                    vm.profile.education = user.worker_detail.education;
                    vm.profile.sectors = user.worker_detail.sectors;
                    vm.profile.tiers = user.worker_detail.tiers;
                    vm.profile.most_recent_role = user.worker_detail.most_recent_role;
                    vm.profile.exp_year = user.worker_detail.exp_year;
                    vm.profile.exp_month = user.worker_detail.exp_month;
                    vm.profile.job_role = (user.experiences[0] && user.experiences[0].job) ?
                                                    user.experiences[0].job.title :
                                                    (user.experiences[0]) ? user.experiences[0].job_role : '';
                    vm.profile.company_name = (user.experiences[0] && user.experiences[0].company) ?
                                                        user.experiences[0].company.name :
                                                        (user.experiences[0]) ? user.experiences[0].company_name : '';
                    vm.profile.user_id = user.id;

                    vm.about_me = {};
                    vm.about_me_general.gender = user.gender;
                    vm.about_me_general.date_of_birth = user.date_of_birth;
                    vm.about_me_general.country_birth = user.country_birth;
                    // vm.about_me_general.marital_status = user.marital_status;

                    vm.about_me_technical.right_to_work = user.worker_detail.right_to_work;
                    vm.about_me_technical.has_tfn = user.worker_detail.has_tfn;
                    vm.about_me_technical.has_abn = user.worker_detail.has_abn;
                    vm.about_me_technical.english_skill = user.worker_detail.english_skill;
                    vm.about_me_technical.drivers_license = user.worker_detail.drivers_license;
                    vm.about_me_technical.drivers_license_state = user.worker_detail.drivers_license_state;
                    vm.about_me_technical.drivers_license_type = user.worker_detail.drivers_license_type;
                    vm.about_me_technical.can_drive_manual = user.worker_detail.can_drive_manual;
                    vm.about_me_technical.australian_tfn = user.worker_detail.australian_tfn;
                    vm.about_me_technical.has_registered_vehicle = user.worker_detail.has_registered_vehicle;
                    vm.about_me_technical.night_shift = user.worker_detail.night_shift;

                    vm.ideal_role = user.worker_detail;
                    vm.ideal_role.countries = user.country;

                    vm.employments = user.experiences;
                    vm.educations = user.educations;
                    vm.tickets = user.tickets;
                    vm.industry_skills = user.skills;

                    Bus.$emit('profileAvatarDetails', user.first_name.charAt(0) + user.last_name.charAt(0));
                    Bus.$emit('userProfileDetails', vm.profile);
                    Bus.$emit('aboutMeGeneralDetails', vm.about_me_general);
                    Bus.$emit('aboutMeTechnicalDetails', vm.about_me_technical);
                    Bus.$emit('idealRoleDetails', vm.ideal_role);
                    Bus.$emit('employmentDetails', vm.employments);
                    Bus.$emit('educationDetails', vm.educations);
                    Bus.$emit('ticketsDetails', vm.tickets, user.worker_detail.has_whitecard);
                    Bus.$emit('industrySkillsDetails', vm.industry_skills, user.worker_detail ? user.worker_detail.main_skill : '');

                    let workerDetail = user.worker_detail ? user.worker_detail : {};

                    workerDetail.skills = user.skills;
                    Bus.$emit('onboardingDetails', workerDetail);

                }).catch(function(error) {
                    /**
                     * NOTE: Please do not delete token if error occurs
                     * Instead, this should be deleted if unauthorized access is returned
                     */
                    // Api.deleteToken();

                    Utils.handleError(error);
                });
            },
        }
    }
</script>
