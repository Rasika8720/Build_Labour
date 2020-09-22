<template>
    <form method="POST" @submit.prevent="registerUser">
        <div class="form-text-header">Registration</div>

        <div class="me-row">
            <div class="role-col-left">
                <!-- <div class="emp-form-label" v-if="input.first_name">First Name</div> -->

                <input id="first_name" type="text" name="first_name" class="form-control" style="padding-left:24px"
                    @focus="hasFocus(false)"
                    v-model="input.first_name" placeholder="First Name" required autofocus />

                <span class="err-msg" v-if="errors.first_name">
                    {{ errors.first_name }}
                </span>
            </div>

            <div class="role-col-right">
                <!-- <div class="emp-form-label" v-if="input.last_name">Last Name</div> -->

                <input id="last_name" type="text" name="last_name" class="form-control" style="padding-left:24px"
                    @focus="hasFocus(false)"
                    v-model="input.last_name" placeholder="Last Name" required autofocus />

                <span class="err-msg" v-if="errors.last_name">
                    {{ errors.last_name }}
                </span>
            </div>
        </div>

        <div class="form-group">
            <!-- <div class="form-col-1">
                <img class="form-mobile-icon" src="/img/icons/au.png"
                    srcset="/img/icons/au@2x.png 2x, /img/icons/au@3x.png 3x">

                <span class="form-col-label">+61</span>
            </div>

            <div class="form-col-2"> -->
                <!-- <div class="emp-form-label" v-if="input.mobile_number">Mobile Number</div> -->

                <input id="mobile_number" type="text" name="mobile_number" class="form-control" style="padding-left:24px"
                    @focus="hasFocus(false)"
                    v-model="input.mobile_number" placeholder="Mobile Number" required />

                <span class="err-msg" v-if="errors.mobile_number">
                    {{ errors.mobile_number }}
                </span>
            <!-- </div> -->
        </div>

<!--        <div class="emp-row">-->
<!--            &lt;!&ndash; <div class="emp-form-label" v-if="input.most_recent_role">Most Recent Role</div> &ndash;&gt;-->

<!--            <input class="form-control" type="text" placeholder="Most Recent Role"-->
<!--                style="padding-left:24px"-->
<!--                v-model="input.most_recent_role"-->
<!--                @focus="hasFocus(true)"-->
<!--                @keyup="onSearchJob(input.most_recent_role)" />-->
<!--            -->
<!--            <span class="err-msg" v-if="errors.most_recent_role">-->
<!--                {{ errors.most_recent_role }}-->
<!--            </span>-->
<!--        </div>-->

        <div class="emp-row" style="margin-top:0" v-if="has_focus && job_roles && job_roles.length > 0">
            <ul class="list-group">
                <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                    @click="onSelectJob(job)">

                    {{ job.job_role_name }}
                </li>
            </ul>
        </div>

        <div class="form-group">
            <!-- <div class="emp-form-label" v-if="input.suburb">Suburb</div> -->

            <input id="suburb" type="text" name="suburb" class="form-control" style="padding-left:24px"
                @focus="hasFocus('locations')"
                   @keyup="onChangeLocation(input.suburb)"
                   @blur="onLeave()"
                v-model="input.suburb" placeholder="Location" required />

            <span class="err-msg" v-if="errors.suburb">
                {{ errors.suburb }}
            </span>

            <div class="emp-row" style="margin-top:0" v-if="locations && locations.length > 0 && has_focus == 'locations'">
                <ul class="list-group">
                    <li class="list-group-item" v-for="(place, idx) in locations" :key="idx"
                        @click="onSelectLocation(place.place_name)">
                        {{ place.place_name }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="form-group">
            <!-- <div class="emp-form-label" v-if="input.email">Email Address</div> -->

            <input id="email" type="email" name="email" class="form-control" style="padding-left:24px"
                @focus="hasFocus(false)"
                v-model="input.email" placeholder="Email Address" required />

            <span class="err-msg" v-if="errors.email">
                {{ errors.email }}
            </span>
        </div>

        <div class="form-group">
            <password-eye ref-name="regTogglePassword"></password-eye>

            <!-- <div class="emp-form-label" v-if="input.password">Password</div> -->

            <input id="password" ref="regTogglePassword" type="password" name="password" class="form-control"
                @focus="hasFocus(false)"
                style="padding-left:24px" v-model="input.password" placeholder="Password" required />

            <span class="err-msg" v-if="errors.password">
                {{ errors.password }}
            </span>
        </div>

        <div class="form-group">
            <password-eye ref-name="regToggleConfirm"></password-eye>

            <!-- <div class="emp-form-label" v-if="input.password_confirmation">Confirm Password</div> -->

            <input id="password-confirm" ref="regToggleConfirm" type="password" class="form-control"
                @focus="hasFocus(false)"
                style="padding-left:24px" name="password_confirmation" v-model="input.password_confirmation"
                placeholder="Confirm Password" required>
        </div>

        <div class="form-group">
            <label for="trainingProvider">
                <input type="checkbox"
                       name="training_provider"
                       id="trainingProvider"
                       v-model="input.training_provider"
                        checked>
                would you like to be contacted by one of our training providers
            </label>
        </div>

        <div class="form-group">
            <a class="btn btn-link pull-left" v-bind:href="endpoints.login">
                Back to login
            </a>

            <button class="pull-right" type="submit" :disabled="disabled">
                {{ loading ? '' : 'Sign Up' }}
            </button>

            <div class="loading">
                <pulse-loader :loading="loading" color="#fff" size="8px"></pulse-loader>
            </div>
        </div>
    </form>
</template>

<script>
    import Api from '@/api';
    import PasswordEye from '../common/PasswordEye';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "register",
        data() {
            return {
                roles: [],
                job_roles: [],
                has_focus: false,
                locations: [],
                loading: false,
                disabled: false,
                input: {
                    first_name: '', last_name: '', mobile_number: '', most_recent_role: '', suburb: '',
                    email: '', password: '', password_confirmation: '', training_provider: true
                },
                errors: {
                    first_name: '', last_name: '', mobile_number: '', most_recent_role: '', suburb: '',
                    email: '', password: '',
                },
                endpoints: {
                    login: '/login',
                    register: '/api/v1/auth/register',
                    onboarding: '/user/onboarding',
                }
            }
        },
        created() {
            let vm = this;

            Bus.$on('regTogglePassword', function(type) {
                vm.$refs['regTogglePassword'].type = type;
            });

            Bus.$on('regToggleConfirm', function(type) {
                vm.$refs['regToggleConfirm'].type = type;
            });
        },
        methods: {
            hasFocus(has_focus) {
                this.has_focus = has_focus;
            },
            async onChangeLocation(keyword) {

                let vm = this;

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler);
                }

                if (keyword && keyword.length > 0) {

                    this.timeoutHandler = await setTimeout(() => {

                        Promise.resolve(Api.getLocationsPromise(keyword)).then((data) => {
                            this.locations = (data.data && data.data.locations) ? data.data.locations.features : [];
                        });
                    }, 400);

                } else {

                    this.locations = [];
                }
            },
            onSelectLocation(location) {
                this.input.suburb = location;

                this.locations = [];
            },
            onSearchJob(keyword) {

                let vm = this;

                if (keyword && keyword.length < 0) {
                    this.job_roles = [];
                }

                Api.searchJobRoles(keyword).then(function(data) {

                    vm.job_roles = data.data ? data.data.job_roles : [];
                });

            },
            onSelectJob(job) {
                this.input.most_recent_role = job.job_role_name;

                this.job_roles = [];
            },
            async registerUser() {
                let vm = this;

                Utils.setObjectValues(vm.errors, '');

                this.loading = true;
                this.disabled = true;

                await axios.post(vm.endpoints.register, vm.$data.input)

                .then(function(response) {
                    let data = response.data;

                    Api.setToken(data.data.token);

                    Api.setNavAvatar(data.data.user.first_name.charAt(0) + data.data.user.last_name.charAt(0), '');

                    window.location.href = vm.endpoints.onboarding;

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                this.loading = false;
                this.disabled = false;
            },
            onLeave(type) {

                if (this.leaveTimeoutHandler) {

                    clearTimeout(this.leaveTimeoutHandler)
                }

                this.leaveTimeoutHandler = setTimeout(() => {

                    this.locations = [];

                }, 2000)
            },
        },
        components: {
            PasswordEye,
            PulseLoader,
        },
    }
</script>
