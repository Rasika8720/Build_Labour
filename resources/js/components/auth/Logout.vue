<template>
    <div class="" id="navbarSupportedContent" v-if="textOnly != 'true'">
        <div id="navbarDropdown" data-toggle="dropdown" >
            <img v-if="input.profile_photo_url" class="bl-nav-profile-pic" :src="input.profile_photo_url" />
            <avatar v-else cls="bl-nav-profile-pic" size="32" border="0" :is-logout=true></avatar>
        </div>

        <ul class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
            <li class="dropdown-item" @click="showProfile">
                My Profile
            </li>
            <li class="dropdown-item" @click="logoutUser">
                Logout
            </li>
        </ul>
    </div>
    <a class="nav-link nav-link-custom" href="#" @click="logoutUser" v-else>
        Logout
    </a>
</template>

<script>

    import Api from '@/api';
    import Avatar from '../common/Avatar';

    export default {
        name: "logout",
        data() {
            return {
                input: {
                    initials: '', profile_photo_url: ''
                },
                endpoints: {
                    logout: '/api/v1/auth/logout',
                }
            }
        },
        props: {
            textOnly: {
                type: String,
            }
        },
        created() {
            let profile_photo_url = Api.getNavAvatar().profile_photo_url;

            if (profile_photo_url) {
                this.input.profile_photo_url = profile_photo_url;
            }

            Bus.$on('triggerGoProfile', () => {
                this.showProfile();
            });

            Bus.$on('triggerGoLogout', () => {
                this.logoutUser();
            });
        },
        methods: {
            showProfile() {
                Api.redirectToProfile();
            },
            logoutUser() {
                let vm = this;

                axios.get(vm.endpoints.logout, Utils.getBearerAuth())
                
                .then(function(response) {
                    Api.deleteToken();
                
                }).catch(function(error) {
                    Utils.handleError(error);

                    Api.deleteToken();
                
                }).finally(function() {
                    Api.deleteToken();
                })
            },
        },
        components: {
            Avatar,
        },
    }
</script>