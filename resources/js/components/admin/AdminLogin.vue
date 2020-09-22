<template>
    <div class="admin-login form-card">
        <form method="POST" @submit.prevent="loginUser">
            <div class="form-text-header">Admin Login</div>

            <div class="form-group">
                <input id="email" type="email"  name="email" class="form-control" style="padding-left:24px"
                       v-model="input.email" placeholder="Email Address" required autofocus>

                <span class="err-msg" v-if="errors.email">
                {{ errors.email }}
            </span>
            </div>

            <div class="form-group">

                <input id="password" ref="loginTogglePassword" type="password"  name="password" class="form-control"
                       style="padding-left:24px" v-model="input.password" placeholder="Password" required>

                <span class="err-msg" v-if="errors.password">
                {{ errors.password }}
            </span>
            </div>

            <div class="form-group" style="text-align: center">
                <button type="submit" :disabled="disabled">
                    Login
                </button>
            </div>
        </form>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "admin-login",
        data() {
            return {
                disabled: false,
                input: {
                    email: '', password: '',
                },
                errors: {
                    email: '', password: '',
                },
                endpoints: {
                    login: '/api/v1/admin/auth/login',
                    profile: '/admin'
                }
            }
        },
        created() {
            let vm = this;

            Bus.$on('loginTogglePassword', function(type) {
                vm.$refs['loginTogglePassword'].type = type;
            });
        },
        methods: {
            async loginUser() {
                let vm = this;

                Utils.setObjectValues(vm.errors, '');

                vm.disabled = true;

                await axios.post(vm.endpoints.login, vm.$data.input)

                .then(function(response) {
                    let data = response.data;

                    Api.setToken(data.data.token);

                    window.location.href = vm.endpoints.profile;
                
                }).catch(function(error) {

                    Utils.setObjectValues(vm.input, '');
                    Utils.handleError(error);
                });

                vm.disabled = false;
            },
        },
    }
</script>