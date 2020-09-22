<template>
    <div></div>
</template>

<script>
    export default {
        data() {
            return {
                company: {
                    photo_url: '', name: '', business_type: '', tier: '',
                    address: '', website: '', phone: '', introduction: '',
                    main_function: '', specialization: [], user_email: ''
                },
                endpoints: {
                    get: '/api/v1/company/',
                },
            }
        },

        props: {
            companyId: {
                type: String,
                required: false
            },
        },

        computed: {
            endpointGet() {
                return this.endpoints.get + this.companyId;
            }
        },

        created() {
            this.getCompany();
        },

        methods: {

            getCompany() {
                let vm = this;

                axios.get(vm.endpointGet, Utils.getBearerAuth())

                .then(function(response) {
                    let company = response.data.data.company;
                    let user = response.data.data.user;

                    vm.company.photo_url = company.photo_url;
                    vm.company.name = company.name;
                    vm.company.business_type = company.business_type;
                    vm.company.tier = company.tier;
                    vm.company.address = company.address;
                    vm.company.website = company.website;
                    vm.company.phone = company.phone;
                    vm.company.introduction = company.introduction;
                    vm.company.main_function = company.main_function;
                    vm.company.main_function_answer = company.main_function_answer;
                    vm.company.specialization = company.specialization;
                    vm.company.user_email = user.email;

                    Bus.$emit('profileAvatarDetails', Utils.getInitials(company.name));
                    Bus.$emit('companyProfileDetails', vm.company);

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },

        }
    }
</script>
