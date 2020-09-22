<template>
    <div id="chat-messages" class="chat-messages">
        <single-message
            v-for="message in messages"
            :key="message.id"
            :message="message">
        </single-message>
        <p v-if="hide_notice === false" class="select-chat-thread">Please Select A Chat</p>
    </div>
</template>

<script>

    import Api from '@/api';
    import SingleMessage from "./messageComponent";
    import Event from '../../../event';
    export default {
        name: "chat-messages",
        components: {SingleMessage},
        data() {
            return {
                messages: [],
                hide_notice: false
            }
        },
        mounted() {
        },
        created() {
            let vm = this;

            Bus.$on('added_message', (message) => {
                vm.messages.push(message);
                vm.hide_notice = true;
                var container = document.getElementById("chat-messages");
                container.scrollTop = container.scrollHeight;
            });
            Bus.$on('getMessages', (messages) => {
                vm.messages = messages;
            });
        }
    }
</script>

<style>
    .chat-messages {
        height: 400px;
        max-height: 400px;
        overflow-y: scroll;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
</style>
