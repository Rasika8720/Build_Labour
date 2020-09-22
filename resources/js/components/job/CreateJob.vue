<template>
    <div class="profile-item-2">
        <div class="profile-content">
            <div class="bl-label-20-style-2">Create Job</div>

            <div class="emp-row mt-3" v-if="creating">
                <input type="text" class="form-control create-job-input" placeholder="Enter Name to save as a template" v-model="template_name">

                <span class="err-msg" v-if="errors.template_name">{{ errors.template_name }}</span>
            </div>

            <div class="emp-row mt-3" v-else>
                <input type="text" class="form-control create-job-input bg-search" placeholder="Search Jobs" v-model="keyword" @keyup="onSearchJobs">
            </div>

            <button v-if="creating" class="mt-4" style="width: 100%;" :disabled="disabled" @click="onClickSaveAsTemplate">
                Save as new template
            </button>

            <div v-else>
                <label class="radio-cont">Saved Templates
                    <input type="checkbox" ref="templates" @click="onClickShowJobs('templates')">
                    <span class="checkmark"></span>
                </label>

                <label class="radio-cont">Active Jobs
                    <input type="checkbox" ref="active" @click="onClickShowJobs('active')">
                    <span class="checkmark"></span>
                </label>

                <label class="radio-cont">Past Jobs
                    <input type="checkbox" ref="closed" @click="onClickShowJobs('closed')">
                    <span class="checkmark"></span>
                </label>
            </div>

            <button class="btn-dark mt-4" style="width: 100%;" :disabled="disabled" @click="onClickPostJob('preview')" v-if="previewBtn">
                Preview
            </button>

            <button class="mt-4" style="width: 100%;" :disabled="disabled" @click="onClickPostJob">
                {{ buttonText }}
            </button>

            <div class="loading" style="bottom: 32px; left: 45%;">
                <pulse-loader :loading="disabled" color="#fff" size="8px"></pulse-loader>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "create-job",
        data() {
            return {

                disabled: false,
                buttonText: '',
                isTemplate: 0,
                isPreview: false,
                template_name: '',
                input: {},
                errors: {
                    template_name: '',
                },
                keyword: '',
                endpoints: {
                    post: '/api/v1/job',
                    save: '/api/v1/job/save-template',
                },
            }
        },
        props: {
            creating: {
                type: Boolean,
                required: true
            },
            companyId: {
                type: String,
                default: null
            },
            previewBtn: {
                type: Boolean,
                default: false
            }
        },
        created() {
            let vm = this;


            Bus.$on('newJobDetails', function(input) {

                console.log('newJobDetails');
                console.log(input);

                vm.input = input;
            });

            Bus.$on('newJobRequirements', function(input) {
                let qua = input.qualifications,
                    exp = input.experience;

                vm.input.min_exp_month = input.min_exp_month;
                vm.input.min_exp_year = input.min_exp_year;

                vm.input.requirements = [
                    { title: 'Qualifications', items: qua[0].course_type != '' && qua[0].qualification_level != '' ? qua : [] },
                    // { title: 'Experience', items: input.min_exp != '' && exp[0] != '' ? { min_exp: input.min_exp, experiences: exp } : [] },
                    { title: 'Skills', items: input.skills[0] != '' ? input.skills : []},
                    { title: 'Tickets', items: input.tickets },
                ];

                vm.input.white_card = input.white_card;
            });

            Bus.$on('newJobResponsibilities', function(input) {
                if (input.responsibilities[0].title != '' && input.responsibilities[0].items[0] != '') {
                    vm.input.responsibilities = input.responsibilities;

                } else {
                    vm.input.responsibilities = [];
                }

                if (vm.isTemplate) {
                    if (vm.input.id) vm.input.id = '';

                    vm.submit(vm.endpoints.save)

                } else {
                    vm.submit(vm.endpoints.post + (vm.input.id ? ('/' + vm.input.id) : ''));
                }
            });

            Bus.$on('editJobPost', function(templateName, status, isTemplate) {
                vm.template_name = templateName;

                if (status == 1 && ! isTemplate) vm.buttonText = 'Save Changes';
            });

            window.onpopstate = function(e) {
                vm.softReload();
            };

            this.buttonText = this.creating ? 'Post Job' : 'Create New Job';
        },
        mounted() {
            this.softReload();
        },
        methods: {
            softReload() {
                if (! this.creating) {
                    this.getJobs(Utils.getUrlParams().type);
                }
            },
            getJobs(refName) {
                this.$refs['templates'].checked = false;
                this.$refs['active'].checked = false;
                this.$refs['closed'].checked = false;

                this.$refs[refName].checked = true;

                Bus.$emit('getJobPosts', refName, this.keyword);
            },
            onClickShowJobs(refName) {
                this.getJobs(refName);

                window.history.pushState({ urlPath: '/job/list?type=' + refName }, '', '/job/list?type=' + refName);
            },
            onSearchJobs(e) {
                Bus.$emit('searchJobPosts', this.keyword);
            },
            onClickSaveAsTemplate() {
                this.isTemplate = 1;

                Bus.$emit('saveJob');
            },

            onClickPostJob(preview) {

                this.isPreview = preview === 'preview' ? true : false;

                if (this.creating) {

                    this.isTemplate = 0;
                    Bus.$emit('saveJob');

                } else {

                    this.creating = true;
                    window.location.href = '/job/post';
                }

            },
            onClickCreateDayLabour() {
                window.location.href = '/job/post-day-labour';
            },
            async submit(endpoint) {
                let vm = this;

                if (this.isTemplate || this.input.is_template) this.input.template_name = this.template_name;
                this.buttonText = '';
                this.disabled = true;

                vm.$data.input.isPreview = vm.isPreview;

                if (Utils.getUrlParams().cache_id) {

                    vm.$data.input.cache_id = Utils.getUrlParams().cache_id;
                }

                await axios.post(endpoint, vm.$data.input, Utils.getBearerAuth())

                .then(function(response) {

                    let data = response.data;

                    // go to job preview page
                    if (data.data.cache) {

                        let href = `/job/preview?cid=${vm.companyId}&cache_id=${data.data.cache.id}`;

                        if (Utils.getUrlParams().jid) {

                            href += '&jid=' + Utils.getUrlParams().jid;
                        }

                        window.location.href = href;
                    }

                    let job = data.data.job;

                    if (job.is_template) {

                       window.location.href = '/job/list?type=templates';

                    } else if (job.status == 1) {

                       window.location.href = '/job/list?type=active';

                    } else {

                       window.location.href = '/job/list?type=closed';

                    }

                }).catch(function(error) {

                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) {
                        vm.errors.template_name = inputErrors.template_name;

                        Bus.$emit('newJobDetailsError', inputErrors);
                        Bus.$emit('newJobRequirementsError', inputErrors);
                    }
                });

                this.disabled = false;
                this.buttonText = this.creating ? 'Post Job' : 'Create New Job';
            },
        },
        components: {
            PulseLoader,
        },
    }
</script>

<style scoped>
    .create-job-input {
        padding: 12px;
        font-size: 13px;
        border-radius: 4px;
        border: solid 1px rgba(0, 0, 0, 0.07);
        background-color: #ffffff;
        height: 46px;
    }
    .create-job-input.bg-search {
        padding-left: 40px;
        background-position: 9px 13px;
        background-repeat: no-repeat;
        background-image: url(/img/icons/search.png);
        background-image: -webkit-image-set(url(/img/icons/search@2x.png) 2x, url(/img/icons/search@3x.png) 3x);
    }
    .create-job-input::placeholder {
        color: #acbbbf;
    }

    /* Customize the label (the radio-cont) */
    .radio-cont {
        display: block;
        position: relative;
        padding-left: 35px;
        cursor: pointer;
        font-size: 15px;
        margin-top: 30px;
        line-height: normal;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .radio-cont:hover {
        color: #00aeef;
    }

    /* Hide the browser's default radio button */
    .radio-cont input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom radio button */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 18px;
        width: 18px;
        border: solid 1px rgba(0, 87, 120, 0.17);
        border-radius: 50%;
    }

    /* When the radio button is checked, add a blue background */
    .radio-cont input:checked ~ .checkmark {
        background-color: #fff;
        border: 1px solid #00aeef;
    }

    /* Create the indicator (the dot/circle - hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the indicator (dot/circle) when checked */
    .radio-cont input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the indicator (dot/circle) */
    .radio-cont .checkmark:after {
        top: 2px;
        left: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #00aeef;
    }
</style>
