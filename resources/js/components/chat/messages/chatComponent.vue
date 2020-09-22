<template>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Messages <span class="users-name" style="float: right; font-weight: bold;">{{user_name}}</span></div>
            <div class="card-body">
                <chat-messages></chat-messages>
                <chat-form></chat-form>
            </div>
        </div>
    </div>
</template>

<script>
    import ChatMessages from "./sections/chat-messages/chatMessagesComponent";
    import ChatForm from "./sections/chat-form/chatFormComponent";
    export default {
        name: "chat",
        components: {ChatForm, ChatMessages},
        data() {
            return {
                user_name: ''
            }
        },
        mounted() {
        },
        created() {
            let vm = this;
            Bus.$on('user_selected', function(user_name) {
                vm.user_name = user_name;
            });
            Bus.$emit('activateTab', 'messages');
        },
        methods: {

        }
    }
</script>
