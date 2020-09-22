<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">
        <div class="me-label">Gender</div>
        <div class="me-row">
            <div class="role-col-left">
                <select v-model="input.gender">
                    <option key="1" value="Male">Male</option>
                    <option key="2" value="Female">Female</option>
                    <option key="3" value="">Other</option>
                </select>
                <span class="err-msg" v-if="errors.gender">
                    {{ errors.gender }}
                </span>
            </div>
        </div>

        <div class="me-label" style="margin-bottom:17px">Date of Birth (optional)</div>
        <div class="me-row">
            <div class="me-col-left">
                <div class="emp-form-label">Day</div>
                <select v-model="birthDay">
                    <option v-for="(day, index) in days" :key="index" v-bind:value="day">{{ day }}</option>
                </select>
            </div>
            <div class="me-col-mid">
                <div class="emp-form-label">Month</div>
                <select v-model="birthMonth" @change="onChangeBirthMonthYear">
                    <option v-for="(month, index) in months" :key="index" v-bind:value="month.id">{{ month.name }}</option>
                </select>
            </div>
            <div class="me-col-right">
                <div class="emp-form-label">Year</div>
                <select v-model="birthYear" @change="onChangeBirthMonthYear">
                    <option v-for="(year, index) in years" :key="index" v-bind:value="year">{{ year }}</option>
                </select>
            </div>
        </div>
        <span class="err-msg" v-if="errors.date_of_birth">
            {{ errors.date_of_birth }}
        </span>

        <!-- <div class="me-label">Country of Birth</div>
        <div class="me-row mb-3">
            <select v-model="input.country_birth">
                <option v-for="country in countries" :key="country" :value="country">{{ country }}</option>
            </select>
        </div>
        <span class="err-msg" v-if="errors.country_birth">
            {{ errors.country_birth }}
        </span> -->
    </form>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "personal-details",
        data() {
            return {
                days: [],
                months: Utils.getMonths(),
                years: Utils.getYears(new Date().getFullYear() - 18),
                birthDay: '',
                birthMonth: '',
                birthYear: '',
                countries: [],
                input: {
                    gender: '', date_of_birth: '', country_birth: '',
                },
                errors: {
                    gender: '', date_of_birth: '', country_birth: '',
                },
                endpoints: {
                    save: '/api/v1/worker/personal-details',
                },
            }
        },

        created() {
            let vm = this;

            Bus.$on('aboutMeGeneralDetails', function(details) {
                if (details) {
                    
                    vm.input.gender = details.gender;
                    vm.input.date_of_birth = details.date_of_birth;
                    vm.input.country_birth = details.country_birth;

                    if (! Utils.isNullOrEmpty(details.date_of_birth)) {
                        let d = new Date(details.date_of_birth);

                        vm.birthDay = d.getDate();
                        vm.birthMonth = d.getMonth() + 1;
                        vm.birthYear = d.getFullYear();

                        vm.days = Utils.getDaysInMonth(vm.birthMonth, vm.birthYear);
                    
                    }
                    // else {
                    //     let d = new Date();
                    //
                    //     vm.birthDay = d.getDate();
                    //     vm.birthMonth = d.getMonth() + 1;
                    //     vm.birthYear = d.getFullYear() - 18;
                    //
                    //     vm.days = Utils.getDaysInMonth(vm.birthMonth, vm.birthYear);
                    // }
                }
            });

            Bus.$on('onboardingSubmitPersonalDetails', async function(action) {

                if (action == 'clear') {
                    Utils.setObjectValues(vm.input, null);
                
                } else {
                    vm.input.date_of_birth = vm.birthYear + '-' + vm.birthMonth + '-' + vm.birthDay;

                    if (! vm.birthYear && ! vm.birthMonth && ! vm.birthDay) {
                        vm.input.date_of_birth = '';
                    }
                }

                Api.submitPromise(vm.endpoints.save, vm.$data.input, Utils.getBearerAuth()).then((data) => {

                    Bus.$emit('aboutMeGeneralDetails', vm.input);
                    vm.errors = [];
                    $('#modalAboutMe').modal('hide');

                }).catch((error) => {

                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;

                });

             //   Api.submit(vm.endpoints.save, vm.$data.input);

            });

            this.days = Utils.getDaysInMonth(this.birthMonth, this.birthYear);
            
            Promise.resolve(Api.getCountries()).then(function(data) {
                vm.countries = data.data.countries;
            });
        },

        methods: {

            onChangeBirthMonthYear() {
                this.days = Utils.getDaysInMonth(this.birthMonth, this.birthYear);
            },

        }
    }
</script>