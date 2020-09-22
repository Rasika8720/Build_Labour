<template>
    <div>
        <div class="loading" style="position: unset; text-align: center;">
            <pulse-loader :loading="loading" color="#00aeef" size="10px"></pulse-loader>
        </div>
        <div v-show="noData && companies.length == 0">
            No data found.
        </div>
        <transition name="slide-fade">
        <div class="profile-item-2">
            <div class="row">
                <div class="col-md-4" v-for="(company, index) in companies" :key="index">
                    <a :href="`/company/profile/${company.id}`">
                        <div class="profile-content ta-center mb-3">
                            <img class="bl-image-80" :src="company.photo_url"
                                 v-if="company.photo_url"
                                 @click="onClickProfilePhoto(company.id)">

                            <div @click="onClickProfilePhoto(company.id)" v-else>
                                <avatar cls="bl-image-80" size="80" border="0" border-radius="100%"
                                        :initials="getInitials(company.name)">
                                </avatar>
                            </div>

                            <div class="bl-label-16 bl-ellipsis">
                                {{ getCompanyName(company) }}
                            </div>
                            <div class="bl-label-14 bl-ellipsis">
                                {{ company.main_function.main_name }}
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
        name: "company-preview",
        data() {
            return {
                loading: false,
                noData: false,
                companies: [],
            }
        },
        props: {
            postType: {
                type: String,
                default: ''
            },
            userId: {
                type: String,
                required: false
            }
        },
        created() {
            let vm = this;

            if (this.postType == 'open_search') {
                Bus.$on('openSearchCompanies', function(results) {
                    vm.show = true;
                    vm.companies = [];
                    vm.noData = false;
                    vm.loading = true;

                    setTimeout(function() {
                        vm.companies = results;
                        vm.loading = false;
                        if (vm.companies.length == 0) vm.noData = true;
                    }, 1000);
                });
            }
        },
        methods: {
            getInitials(name) {
                return Utils.getInitials(name);
            },
            onClickProfilePhoto(id) {
                Api.redirectToCompanyProfile(id);
            },
            getCompanyName(company) {

                if (this.userId && company) {

                    if (this.userId == company.created_by) {

                        return '(Me) ' + company.name;

                    } else {

                        return company.name;
                    }
                }

                return '';
            }
        },
        components: {
            Avatar,
            PulseLoader,
        },
    }
</script>