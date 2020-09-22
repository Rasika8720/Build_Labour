<template>
    <div>
        <form class="form">
            <textarea
                cols="25"
                rows="5"
                class="form-input"
                @keydown="typing"
                v-model="body">
            </textarea>
            <span class="notice">
                <button type="button"
                        class="upload-file btn btn-secondary"
                        @click="upload"
                        style="margin-top: 0; margin-left: 10px; float: right;"
                        disabled>Upload</button>
                <input type="file" ref="upload" id="upload" value="Choose a file" accept="image/*, .pdf" style="display:none" @change="onFileChange" />
                <button type="button"
                        class="send-message btn btn-primary"
                        @click="sendMessage"
                        style="margin-top: 0; float: right;"
                        disabled>Send</button>
            </span>
        </form>
    </div>
</template>

<script>
    import Event from '../../../event';
    import Api from '@/api';

    export default {
        name: "chat-form",
        mounted() {
            Bus.$on('added_message', function() {
                document.getElementsByClassName('chat-messages')[0].scrollTop = 0;
            });
        },
        data() {
            return {
                body: null
            }
        },
        methods: {
            typing(e) {
                if(e.keyCode === 13 && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            },
            sendMessage() {
                if(!this.body || this.body.trim === '')
                    return;

                let messageObj = this.buildMessage();

                if( messageObj !== false ) {
                    Api.submit('/api/v1/chat/send', messageObj, 'added_message', true);
                    this.body = null;
                } else {
                    this.body = 'Request Is Pending. Messages Can Not Be Sent Until Request Has Been Accepted.';
                }

            },
            buildMessage() {
                let selected = document.getElementsByClassName('selected')[0],
                    status = selected.getAttribute('data-status'),
                    userId = selected.getAttribute('data-user_id'),
                    currentId = null;

                if(status !== 'pending') {
                    Api.getCurrentUserId().then(function (id) {
                        currentId = id;
                    });

                    var today = new Date();
                    var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                    var dateTime = date+' '+time;

                    return {
                        recipient: userId,
                        message: this.findLinks(this.body),
                        current_id: currentId,
                        message_type: 'message',
                        sent_at: dateTime
                    }
                } else {
                    return false
                }
            },
            findLinks(string)
            {
                var urlRegex = /(https?:\/\/[^\s]+)/g;
                return string.replace(urlRegex, function(url) {
                    return '<a href="' + url + '" target="_blank">' + url + '</a>';
                });
            },
            removeScript(string) {
                string.replace('<', '&gt;');
            },
            upload() {
                upload.click();
            },
            onFileChange() {

                let messageObj = this.buildUpload();
                console.log(this.$refs.upload);

                if( messageObj !== false ) {
                    axios.post(
                        '/api/v1/chat/send',
                        messageObj,
                        {
                            headers: {
                                "Authorization" : "Bearer " + Api._getBearerToken(),
                                'Content-Type': 'multipart/form-data'
                            },
                        }).then(function(response) {

                            messageObj.message = response.data;
                            Bus.$emit('added_message', messageObj);

                        }).catch(function(error) {
                        console.log(error.response.data.message);
                        // Bus.$emit('alertError', error.response.data.message);
                        Utils.handleError(error);
                    });
                } else {
                    this.body = 'Request Is Pending. Messages Can Not Be Sent Until Request Has Been Accepted.';
                }

                this.$refs.upload.files = null;
            },
            buildUpload() {
                let selected = document.getElementsByClassName('selected')[0],
                    status = selected.getAttribute('data-status'),
                    userId = selected.getAttribute('data-user_id'),
                    currentId = null;

                if(status !== 'pending') {
                    Api.getCurrentUserId().then(function (id) {
                        currentId = id;
                    });

                    var today = new Date();
                    var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
                    var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                    var dateTime = date+' '+time;

                    let formData = new FormData();
                    formData.append('recipient', userId);
                    formData.append('file_message', this.$refs.upload.files[0]);
                    formData.append('current_id', currentId);
                    formData.append('message_type', 'file');
                    formData.append('sent_at', dateTime);

                    return formData;
                } else {
                    return false
                }
            }
        }
    }
</script>

<style>
    .form {
        padding: 8px;
    }
    .form-input {
        width: 100%;
        border: 1px solid #d3e0e9;
        padding: 5px 10px;
        outline: none;
    }
    .notice {
        color: #aaa
    }

</style>
