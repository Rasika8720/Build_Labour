<template>
    <div>
        <div class="loading" style="position: unset; text-align: center;">
            <pulse-loader :loading="loading" color="#00aeef" size="10px"></pulse-loader>
        </div>
        <div v-show="noData && employees.length == 0">
            No data found.
        </div>
        <transition name="slide-fade">
        <div class="profile-item-2" v-if="show">
            <div class="row">
                <div class="col-md-4" v-for="(employee, index) in employees" :key="index">
                    <a :href="`/user/profile/` + (employee.user_id ? employee.user_id : employee.id)">
                        <div class="profile-content ta-center mb-3">
                            <div v-if="employee.user_id">
                                <img class="bl-image-80" :src="employee.profile_photo_url"
                                     v-if="employee.profile_photo_url"
                                     @click="onClickProfilePhoto((employee.user_id ? employee.user_id : employee.id))">

                                <div @click="onClickProfilePhoto((employee.user_id ? employee.user_id : employee.id))" v-else>
                                    <avatar cls="bl-image-80" size="80" border="0" border-radius="100%"
                                            :initials="getInitials(employee.full_name)">
                                    </avatar>
                                </div>
                            </div>
                            <div v-else>
                                <img class="bl-image-80" :src="employee.profile_photo_url"
                                     v-if="employee.profile_photo_url"
                                     @click="onClickProfilePhoto((employee.user_id ? employee.user_id : employee.id))">

                                <div @click="onClickProfilePhoto((employee.user_id ? employee.user_id : employee.id))" v-else>
                                    <avatar cls="bl-image-80" size="80" border="0" border-radius="100%"
                                            :initials="getInitials(employee.full_name)">
                                    </avatar>
                                </div>
                            </div>

                            <div class="bl-label-16 bl-ellipsis">
                                {{ employee.first_name }} {{ employee.last_name.substr(0,1)}}.
                            </div>
                            <div class="bl-label-14 bl-ellipsis">
                                {{ employee.job_role }}
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        </transition>
    </div>
</template>

<script>
    import Api from '@/api';
    import Avatar from '../common/Avatar';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "company-people",
        data() {
            return {
                show: false,
                loading: false,
                noData: false,
                employees: [],
                endpoints: {
                    get: '/api/v1/company/',
                    company: '/api/v1/company'
                },
            }
        },
        props: {
            companyId: {
                type: String,
                required: false
            },
            userId: {
                type: String,
                required: false
            },
            postType: {
                type: String,
                default: ''
            },
        },
        computed: {
            endpointGet() {
                return this.endpoints.get + this.companyId + '/posts/people';
            }
        },
        created() {
            let vm = this;

            if (this.postType == 'open_search') {
                Bus.$on('openSearchIndividuals', function(results) {
                    vm.show = true;
                    vm.employees = [];
                    vm.noData = false;
                    vm.loading = true;

                    setTimeout(function() {
                        vm.employees = results;
                        vm.loading = false;
                        if (vm.employees.length == 0) vm.noData = true;
                    }, 1000);
                });

            } else {
                Bus.$on('showCompanyPeople', function(flag) {
                    vm.show = flag;
                    vm.getEmployees();

                    Bus.$emit('hideCompanyJobs');
                    Bus.$emit('hideCompanyPosts');
                });

                Bus.$on('hideCompanyPeople', function() {
                    vm.show = false;
                });
            }
        },
        methods: {
            getInitials(name) {
                return Utils.getInitials(name);
            },
            getEmployees(endpoint) {
                let vm = this;

                // Promise.resolve(Api.getEmployees(vm.companyId)).then(function(data) {
                //     vm.employees = data.data.people;
                // });

                Promise.resolve(Api.getCompanyApplicants(vm.endpoints.company + `/${vm.companyId}/applicants`)).then(function(data) {

                    vm.employees = data.data.applicants;
                });
            },
            onClickProfilePhoto(id) {
                Api.redirectToUserProfile(id);
            },
            getFullName(employee) {

                console.log(employee);

                if (this.userId && employee) {

                    let userId = employee.user_id ? employee.user_id : employee.id;

                    if (this.userId == userId) {

                        return 'Me';

                    } else {

                        return employee.full_name;
                    }
                }

                return employee.full_name ? employee.full_name : '' ;
            }
        },
        components: {
            Avatar,
            PulseLoader,
        },
    }
</script>
