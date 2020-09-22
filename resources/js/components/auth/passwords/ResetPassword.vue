<template>
    <form method="POST" @submit.prevent="resetPassword">
        <div class="form-text-header">Reset Password</div>
        
        <div class="form-text">
            Enter your new password for {{ email_formatted }}
        </div>

        <div class="form-group">
            <password-eye ref-name="resetTogglePassword"></password-eye>
            
            <input id="password" ref="resetTogglePassword" type="password" name="password" class="form-control" 
                style="padding-left:24px" v-model="input.password" placeholder="Password" required autofocus />

            <span class="err-msg" v-if="errors.password">
                {{ errors.password }}
            </span>
        </div>

        <div class="form-group">
            <password-eye ref-name="resetToggleConfirm"></password-eye>

            <input id="password-confirm" ref="resetToggleConfirm" type="password" class="form-control" 
                style="padding-left:24px" name="password_confirmation" v-model="input.password_confirmation" placeholder="Confirm Password" required>
        </div>
        
        <div class="form-group">
            <a class="btn btn-link pull-left" v-bind:href="endpoints.login">
                Back to login
            </a>
            
            <button class="pull-right" type="submit" :disabled="loading">
                {{ loading ? '' : 'Done' }}
            </button>

            <div class="loading">
                <pulse-loader :loading="loading" color="#fff" size="8px"></pulse-loader>
            </div>
        </div>
    </form>
</template>

<script>
    import PasswordEye from '../../common/PasswordEye';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "reset-password",
        data() {
            return {
                loading: false,
                email_formatted: '',
                input: {
                    email: '',
                    token: '',
                    password: '',
                    password_confirmation: ''
                },
                errors: {
                    email: '',
                    password: '',
                },
                endpoints: {
                    login: '/login',
                    reset: '/api/v1/password/reset'
                }
            }
        },
        created() {
            let url_params = Utils.getUrlParams();

            if (url_params.hasOwnProperty('email') && url_params.hasOwnProperty('token')) {
                this.input.email = url_params.email;
                this.input.token = url_params.token;
                
                let split = this.input.email.split('@', 2),
                    first = split[0],
                    second = '@' + split[1];

                this.email_formatted = first.charAt(0).toUpperCase() + first.substr(0, 3).slice(1) + 
                    first.substr(3).replace(/./g, '*') + second;
            }

            let vm = this;

            Bus.$on('resetTogglePassword', function(type) {
                vm.$refs['resetTogglePassword'].type = type;
            });

            Bus.$on('resetToggleConfirm', function(type) {
                vm.$refs['resetToggleConfirm'].type = type;
            });
        },
        methods: {  
            async resetPassword() {
                let vm = this;
                
                Utils.setObjectValues(vm.errors, '');

                this.loading = true;

                await axios.post(vm.endpoints.reset, vm.$data.input)
                    
                .then(function(response) {
                    let data = response.data;
                    
                    window.location.href = vm.endpoints.login;

                    Bus.$emit('alertSuccess', data.message);
                
                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);
        
                    if (inputErrors) vm.errors = inputErrors;
                });
                
                this.loading = false;
            },
        },
        components: {
            PasswordEye,
            PulseLoader,
        },
    }
</script>