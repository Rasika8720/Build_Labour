<template>
    <div class="chat__message self" v-if="message.sent_by == message.current_user">
        <strong class="user" >Me</strong>
        <span class="message__sent-at" v-if="message.formatted_time">
                    {{message.formatted_time + ' - ' + message.formatted_date }}
        </span>
        <span class="message__sent-at" v-else>
                    Now
        </span>
        <p class="body">
            <span class="message__item" v-if="message.message_type == 'file'">
                <a :href="message.message" target="_blank">
                    {{message.message.split('/')[4]}}
                </a>
            </span>

            <span class="message__item" v-html="message.message" v-if="message.message_type == 'message'">
            </span>
        </p>
    </div>
    <div class="chat__message other" v-else>
        <span class="message__sent-at">
                    {{message.formatted_time + ' - ' + message.formatted_date }}
        </span>
        <strong class="user">{{name}}</strong>
        <p class="body">
            <span class="message__item" v-if="message.message_type == 'file'">
                <a :href="message.message" target="_blank">
                    {{message.message.split('/')[4]}}
                </a>
            </span>

            <span class="message__item" v-html="message.message" v-if="message.message_type == 'message'">
            </span>
        </p>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "single-message",
        props: ['message'],
        data() {
            return {
                name: document.getElementsByClassName('users-name')[0].textContent
            }
        },
        mounted() {
            var container = document.getElementById("chat-messages");
            container.scrollTop = container.scrollHeight;
            console.log(this.message);
        }
    }
</script>

<style>
    .body {
        margin-bottom: 0;
    }
    .chat__message {
        background-color:#cecece;
        margin-left: 60px;
        margin-top: 20px;
        padding: 10px;
    }
    .self {
        background-color: #f0f0f0;
        padding: 10px;
        margin-left: 0;
        margin-right: 60px;
    }

    .self + .self {
        margin-top: 5px;
    }

    .other + .other {
        margin-top: 5px;
    }

    .self + .self .user {
        display: none;
    }

    .other + .other .user {
        display: none;
    }

    .message__sent-at {
        float: right;
        font-size: 12px;
    }
</style>
