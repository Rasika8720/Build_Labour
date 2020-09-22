<template>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header" @click="openThreadContainer">Threads</div>
            <div class="card-body user-card" ref="threadContainer">
                <div class="users">
                    <div class="accepted"
                         v-for="user in users.Accepted"
                         :key="user.id">
                        <a href="#" :id="'user-'+user.id" :data-user_id="user.id"
                            :data-user_name="user.first_name ? user.first_name + ' ' + user.last_name : user.company_name"
                           @click="linkClick">
                            <p class="image" v-if="user.profile_photo_url!=null">
                                <img :src="user.profile_photo_url" width="40" height="40">
                            </p>
                            <p class="image" v-else-if="user.company_image!=null">
                                <img :src="user.company_image" width="40" height="40">
                            </p>
                            <p class="image" v-else>
                                <svg class="profile-picture"
                                     style="width: 40px; height: 40px; border-radius: 100%; background: rgb(240, 242, 244); border: 0px solid rgb(255, 255, 255);">
                                    <text x="50%" y="50%" text-anchor="middle" dominant-baseline="central" style="fill: rgb(162, 178, 183); font-family: Inter; font-size: 44px;">
                                        ?
                                    </text>
                                </svg>
                            </p>
                            <p class="username">
                                <span v-if="user.first_name!=null" :class="{'text-bold': user.chat_history_count}">{{user.first_name+ ' ' + user.last_name}}</span>
                                <span v-else :class="{'text-bold': user.chat_history_count}">{{user.company_name}}</span>
                            </p>
                        </a>
                        <a :href="user.profile_link" class="profile-link" target="_blank">View Profile</a>
                    </div>
                    <p v-if="!users.Accepted"  style="text-align: center;">
                        No Active Chats
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import Event from '../event';
    export default {
        name: 'user-component',
        data() {
            return {
                users: []
            }
        },
        props: {
            userId: {
                type: String,
                required: false
            }
        },
        mounted() {
            Event.$on('users.here', (users) => {
                this.users = users;
            });

            let vm = this;
            this.resetMessageCount();
            this.getChatUsers();

            setTimeout(function(){document.getElementById('user-'+vm.userId).click()}, 1500);

        },
        methods: {
            openThreadContainer()
            {
                this.$refs['threadContainer'].classList.toggle('hide');
            },
            linkClick(e) {
                this.openThreadContainer();
                let chatSelectText = document.getElementsByClassName('select-chat-thread')[0];
                if(undefined !== chatSelectText)
                    document.getElementsByClassName('select-chat-thread')[0].remove();

                Bus.$emit('user_selected', e.target.closest('a').getAttribute('data-user_name'));
                document.getElementsByClassName('send-message')[0].removeAttribute('disabled');
                document.getElementsByClassName('upload-file')[0].removeAttribute('disabled');
                this.getChatUsers();
                this.setSelectedUser(e);
                this.getChatHistory(e);

                let vm = this;
                setTimeout(function()
                {
                    vm.updateChatHistory();
                }, 10000);
            },
            getChatUsers() {
                let vm = this;
                axios.get('/api/v1/chat/connected-users', Utils.getBearerAuth())
                    .then(function(response) {
                        console.log(response);
                        vm.users = response.data.data;
                    });
            },
            getChatHistory(e)
            {
                let vm = this;
                const request_user_id = e.target.closest('a').getAttribute('data-user_id');

                axios.get('/api/v1/chat/history/' + request_user_id, Utils.getBearerAuth())
                    .then(function(response) {
                        Bus.$emit('getMessages', response.data.data.messages)
                        console.log(response.data.data);
                    });
            },
            updateChatHistory()
            {
                console.log('looking for new messages');
                const request_user_id = document.getElementsByClassName('selected')[0].getAttribute('data-user_id');
                axios.get('/api/v1/chat/history/' + request_user_id, Utils.getBearerAuth())
                    .then(function(response) {
                        Bus.$emit('getMessages', response.data.data.messages)
                        console.log(response.data.data);
                    });
                setTimeout(this.updateChatHistory, 10000 );
            },
            setSelectedUser(e)
            {
                let selected = document.getElementsByClassName('selected')[0];
                console.log(selected);
                if(undefined !== selected) {
                    selected.classList.remove('selected');
                }

                let data = {
                    'chat_partner_id': e.target.closest('a').getAttribute('data-user_id')
                };
                Api.submit('/api/v1/chat/update-seen-by',data );

                e.target.closest('a').classList.add('selected');
                return true;
            },
            resetMessageCount()
            {
                axios.get('/api/v1/chat/reset-unread', Utils.getBearerAuth())
                    .then(function(response) {
                        Bus.$emit('unreadCountReset');
                    });
            }
        }
    }
</script>

<style>
    .users {
        background-color: #fff;
        border-radius: 3px;
    }
    .selected .username span {
        color: #45c145;
    }
    .accept-reject {
        position: absolute;
        right: 0;
        top: 11px;
    }
    .text-bold {
        font-weight: 600;
    }

    @media only screen and (max-width: 767px) {
        .user-card {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .user-card.hide {
            display: none;
        }

        .user-card .users {
            background: #fff;
            max-width: 600px;
            width: 100%;
            padding: 20px;
            border-radius: 9px;
            max-height: 300px;
            overflow-y: auto;
        }
    }
</style>
