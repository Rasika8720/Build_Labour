<template>
    <form method="POST" @submit.prevent="sendResetPasswordLink">
        <div class="form-text-header">Forgot Password</div>

        <div class="form-text">
            Enter your Email Address to receive an email with a link to reset your password
        </div>

        <div class="form-group">
            <input id="email" type="email" name="email" class="form-control" style="padding-left:24px"
                v-model="input.email" placeholder="Email Address" required autofocus />

            <span class="err-msg" v-if="errors.email">
                {{ errors.email }}
            </span>
        </div>

        <div class="form-group">
            <a class="btn btn-link pull-left" v-bind:href="endpoints.login">
                Back to login
            </a>
            
            <button class="pull-right" type="submit" :disabled="disabled">
                {{ loading ? '' : 'Send Reset' }}
            </button>

            <div class="loading">
                <pulse-loader :loading="loading" color="#fff" size="8px"></pulse-loader>
            </div>
        </div>
    </form>
</template>

<script>
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "request-link",
        data() {
            return {
                loading: false,
                disabled: false,
                input: {
                    email: '',
                },
                errors: {
                    email: '',
                },
                endpoints: {
                    login: '/login',
                    send: '/api/v1/password/email'
                }
            }
        },
        methods: {
            async sendResetPasswordLink() {
                let vm = this;
                
                this.errors.email = '';
                this.loading = true;
                this.disabled = true;

                await axios.post(vm.endpoints.send, vm.$data.input)

                .then(function(response) {
                    let data = response.data;
                    
                    vm.input.email = '';

                    Bus.$emit('alertSuccess', data.message);
                
                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);
    
                    if (inputErrors) vm.errors = inputErrors;
                });
                
                this.loading = false;
                this.disabled = false;
            }
        },
        components: {
            PulseLoader,
        },
    }
</script>