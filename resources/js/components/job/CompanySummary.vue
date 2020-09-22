<template>
    <div class="profile-item-1">
        <div class="company-image">
            <img v-if="photo_url" :src="photo_url">
            <avatar v-else cls="" size="110" border="0" border-radius="8px"></avatar>
        </div>

        <div class="profile-content" style="margin-top:-56px">
            <div class="profile-content-p20 pb-4">
                <div class="company-title">
                    {{ name }}
                </div>

                <div class="company-address">
                    {{ address }}
                </div>

                <div class="company-body">
                    {{ introduction }}
                </div>

                <div class="company-view" v-if="show">
                    <a :href="'/company/profile/' + id">
                        View Business
                    </a>
                </div>

                <div class="company-view" v-if="isPreviewMode">
                    <a href="javascript:void(0)" @click="onClickContinuePreview">This is a Preview Mode, just click here to continue posting this job.</a>
                </div>

            </div>

        </div>
        <div v-if="user_applied === '0' ">
            <button style="width: 100%;" v-if="user_role === 'Worker' && show" @click="apply">
                {{buttonText }}

                <div class="loading" style="position: unset; text-align: center;">
                    <pulse-loader :loading="loading" color="#fff" size="10px"></pulse-loader>
                </div>
            </button>
        </div>
        <div v-else>
            <button style="width: 100%;" :style="{backgroundColor: btn_disabled_color}">
                {{buttonText }}

                <div class="loading" style="position: unset; text-align: center;">
                    <pulse-loader :loading="loading" color="#fff" size="10px"></pulse-loader>
                </div>
            </button>
        </div>

        <alert></alert>

    </div>
</template>

<script>
    import Avatar from '../common/Avatar';
    import Alert from '../common/Alert';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "company-summary",
        data() {
            return {
                loading: false,
                show: true,
                disabled: false,
                id: '',
                photo_url: '',
                name: '',
                address: '',
                introduction: '',
                job_id: '',
                endpoints: {
                    apply: '/api/v1/job/',
                },
                buttonText: '',
                user_role: '',
                user_applied: '',
                btn_disabled: 'enabled',
                btn_disabled_color: '',
            }
        },
        props: {
            role:{
                type:String,
                required:true
            },
            already_applied:{
                type:String,
                required:true
            },
            isPreviewMode: {
                type: Number,
                default: 0
            }
        },
        created() {
            let vm = this;

            Bus.$on('companySummaryDetails', function(details, action) {
                if (details) {
                    vm.id = details.id;
                    vm.photo_url = details.photo_url;
                    vm.name = details.name;
                    vm.address = details.address;
                    vm.introduction = details.introduction;
                }

                if (action == 'new') {
                    vm.show = false;
                }
            });

            Bus.$on('jobDetails', function(details) {
                if (details) {
                    vm.job_id = details.id;
                }
            });

            this.buttonText = vm.loading ? '' : 'Apply';
            vm.user_role = vm.role;
            vm.user_applied = vm.already_applied;

            if(vm.user_applied == '1'){
                vm.buttonText = 'Already Applied';
                vm.btn_disabled_color = '#A2A2A2';
            }


        },
        methods: {

            onClickContinuePreview() {

                console.log('onClickContinuePreview');

                if (Utils.getUrlParams().cache_id) {

                    let href = '/job/post?cache_id=' + Utils.getUrlParams().cache_id;

                    if (Utils.getUrlParams().jid) {

                        href += '&jid=' + Utils.getUrlParams().jid;
                    }

                    window.location.href = href;

                }
            },
            async apply(){
                let vm = this;
                vm.loading = true;
                vm.buttonText = '';
                Utils.setObjectValues(this.errors, '');

                await axios.get(vm.endpoints.apply+vm.job_id+'/apply', Utils.getBearerAuth())
                .then(function(response) {
                    if(response.data.success){
                        Bus.$emit('alertSuccess', response.data.message);
                        vm.user_applied = '1';
                        vm.buttonText = 'Already Applied';
                        vm.btn_disabled_color = '#A2A2A2';
                    }
                    else{
                        Bus.$emit('alertError', response.data.message);
                        vm.buttonText = 'Apply';
                    }

                    vm.loading = false;

                }).catch(function(error) {
                    Bus.$emit('alertError', error.response.data.message);
                    vm.loading = false;
                    vm.buttonText = 'Apply';

                });
            }
        },
        components: {
            Avatar,Alert,PulseLoader,
        },
    }
</script>

<style Scoped>
	.alert-close {
		margin-top: 6px !important;
		margin-right: -20px !important;

	}
	.alert-icon {
		width: 20px;
		margin-left: 0!important;
		margin-right: 0 !important;
	}

    .alert-main{
        margin-top: 30px !important;
    }
    #app>.alert-main{
        display:none !important;
    }
</style>
