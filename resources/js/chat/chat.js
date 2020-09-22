import Vue from 'vue';

import Navigation from '../components/common/Navigation';
import Chat from '../components/chat/messages/chatComponent';
import userComponent from "../components/chat/connections/userComponent";

new Vue({
    el: "#app",
    components: {
        Navigation,
        userComponent,
        Chat
    },
});
