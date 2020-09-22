<template src="./JobApplicantsCompare.htm"></template>

<script>
    import Api from '@/api';
    import EmploymentListCompare from '../profile/EmploymentListCompare';
    import EducationListCompare from '../profile/EducationListCompare';

    export default {
        name: "job-applicants-compare",
        components: {
            EmploymentListCompare,
            EducationListCompare
        },
        data() {
            return {
                disabled: false,
                applicants: [],
                job: null,
                stats: null,
                sortBy: 'Most Recent',
                searchKeyword: '',
                endpoints: {
                    getCompany: '/api/v1/company/',
                    getJob: '/api/v1/company/',
                    getApplicants: '/api/v1/job/',
                    getStats: '/api/v1/job/',
                    doScore: '/api/v1/job/'
                },
                imgSrcExpand: '/img/icons/plus.png',
                imgSrcSetExpand: '/img/icons/plus@2x.png 2x, /img/icons/plus@3x.png 3x',
            }
        },
        created() {

            let companyId = this.companyId ? this.companyId : Utils.getUrlParams().cid;

            this.endpoints.getJob = this.endpoints.getJob + companyId + '/jobs/' + Utils.getUrlParams().jid;
            this.endpoints.getCompany = this.endpoints.getCompany + companyId;
            this.endpoints.getApplicants = this.endpoints.getApplicants + Utils.getUrlParams().jid + '/applicants';
            this.endpoints.getStats = this.endpoints.getStats + Utils.getUrlParams().jid + '/stats';
            this.endpoints.doScore = this.endpoints.doScore + Utils.getUrlParams().jid + '/do-score';

            this.searchApplicants();
            this.getStats();
        },
        methods: {
            getUserIds()
            {
                let regex = /[?&]([^=#]+)=([^&#]*)/g,
                    url = window.location.href,
                    params = '',
                    match;


                while(match = regex.exec(url)) {
                    if(match[1] == 'userIds[]')
                        params += match[0];
                }

                return params;
            },
            onClickExpandEmployments(index) {

                let key = 'employments' + index;
                let imgKey = 'EmploymentsToggleImg-' + index;

                if (this.$refs[key][0].classList.contains("expand")) {

                    this.$refs[imgKey][0].src = '/img/icons/plus.png';
                    this.$refs[key][0].classList.remove("expand");

                } else {

                    this.$refs[key][0].className = 'item employments expand';
                    this.$refs[imgKey][0].src = '/img/icons/minus.png';
                }


            },

            onClickExpandEducations(index) {

                let key = 'educations' + index;
                let imgKey = 'EducationsToggleImg-' + index;

                if (this.$refs[key][0].classList.contains("expand")) {

                    this.$refs[imgKey][0].src = '/img/icons/plus.png';
                    this.$refs[key][0].classList.remove("expand");
                } else {

                    this.$refs[key][0].className = 'item educations expand';
                    this.$refs[imgKey][0].src = '/img/icons/minus.png';
                }


            },

            onClickExpandTickets(index) {

                let key = 'tickets' + index;
                let imgKey = 'TicketsToggleImg-' + index;

                if (this.$refs[key][0].classList.contains("expand")) {

                    this.$refs[imgKey][0].src = '/img/icons/plus.png';
                    this.$refs[key][0].classList.remove("expand")

                } else {

                    this.$refs[key][0].className = 'item tickets expand';
                    this.$refs[imgKey][0].src = '/img/icons/minus.png';
                }

            },

            onClickExpandSkills(index) {

                let key = 'skills' + index;
                let imgKey = 'SkillsToggleImg-' + index;

                if (this.$refs[key][0].classList.contains("expand")) {

                    this.$refs[imgKey][0].src = '/img/icons/plus.png';
                    this.$refs[key][0].classList.remove("expand");

                } else {

                    this.$refs[key][0].className = 'item skills expand';
                    this.$refs[imgKey][0].src = '/img/icons/minus.png';
                }

            },

            onClickExpandNextRole(index) {

                let key = 'next-role' + index;
                let imgKey = 'NextRoleToggleImg-' + index;

                if (this.$refs[key][0].classList.contains("expand")) {

                    this.$refs[imgKey][0].src = '/img/icons/plus.png';
                    this.$refs[key][0].classList.remove("expand");
                } else {

                    this.$refs[key][0].className = 'item next-role expand';
                    this.$refs[imgKey][0].src = '/img/icons/minus.png';
                }

            },

            onClickExpandProfileDescription(index) {

                let wrapperKey = 'wrapper-profile-description' + index;
                let key = 'profile-description' + index;
                let hrefKey = 'p-description-read-more-' + index;

                if (this.$refs[key][0].classList.contains("expand")) {

                    this.$refs[wrapperKey][0].classList.remove("expand");
                    this.$refs[key][0].classList.remove("expand");
                    this.$refs[hrefKey][0].innerHTML  = 'Read more..';

                } else {

                    this.$refs[wrapperKey][0].className = 'bl-mb20 profile-description expand';
                    this.$refs[key][0].className = 'p-description-content expand';
                    this.$refs[hrefKey][0].innerHTML  = 'Close..';
                }

            },

            isLimitWords(sentence, noOfWords = 5) {

                if (sentence && sentence.split(" ").length > noOfWords) {

                    return true;
                }

                return false;
            },

            getNoWords(sentence, noOfWords = 5) {

                if (sentence && sentence.split(" ").length > noOfWords) {

                    return sentence.split(" ").slice(0, noOfWords).join(" ") + "...";
                }

                return sentence;
            },
            parseEnglish(engVal) {

                return engVal == 1 ?
                    'Competent in written and spoken' :
                    'Not competent in written and spoken';
            },

            parseDriversLicense(val) {

                return val != 'none' ?
                    'Owns valid licence' :
                    'Does not own valid licence';
            },

            parseRightToWork(val) {
                return val == 1 ? 'Yes, I have right to work in Australia' : 'No, I don\'t have right to work in Australia';
            },

            parseStates(states) {

                return states ? '' : states
            },
            getApplicants() {

                let vm = this;
                axios.get(vm.endpoints.getApplicants, Utils.getBearerAuth())

                    .then(function(response) {

                        vm.applicants = response.data.data.applicants;
                        console.log(response.data.data);
                    })
            },

            goToJobs() {

                if (window.location.pathname != '/job/list?type=templates') {
                    window.location = '/job/list?type=templates';
                }
            },

            goToCompare() {

                let companyId = Utils.getUrlParams().cid;
                let jobId =  Utils.getUrlParams().jid;

                window.location = '/job/applicants?cid=' + companyId + '&jid=' + jobId + '&compare=true';
            },

            goToProfile(userId) {

                if (window.location.pathname != '/user/profile/' + userId) {
                    window.location = '/user/profile/' + userId;
                }
            },

            getStats() {

                let vm = this;
                axios.get(vm.endpoints.getStats, Utils.getBearerAuth())

                    .then(function(response) {

                        vm.stats = response.data.data.stats;
                    })
            },

            getFavouriteImg(userId) {

                if (this.stats.favourites.includes(userId)) {

                    return '/img/icons/heart_selected.png';
                }

                return '/img/icons/heart.png';

            },

            getNotSuitImg(userId) {

                if (this.stats.not_suitable.includes(userId)) {

                    return '/img/icons/thumbs-down_selected.png';
                }

                return '/img/icons/thumbs-down.png';

            },

            doScore(userId, category) {

                let vm = this;

                axios.post(vm.endpoints.doScore, {scored_to: userId, category: category}, Utils.getBearerAuth())

                    .then(function(response) {

                        vm.stats.favourites = response.data.data.favourites;
                        vm.stats.not_suitable = response.data.data.not_suitable;

                    })
            },

            selectSort(type) {

                let vm = this;

                vm.sortBy = type;
            },

            async searchApplicants() {

                let vm = this,
                    applicantString = this.getUserIds();

                await axios.get(vm.endpoints.getApplicants + '?keyword=' + vm.searchKeyword + '&sort=' + vm.sortBy + applicantString, Utils.getBearerAuth()).then(function(response) {

                    console.log(response.data.data.applicants);
                    vm.applicants = response.data.data.applicants;

                }).catch(function(error) {

                });
            }
        }
    }
</script>

<style scoped>

    .card {
        background-color: transparent;
        background-clip: unset;
        border: none;
    }

    .item.shortlist-options {
        position: absolute;
        top: 10px;
        right: 0;
        padding: 0;
        width: 180px;
        border-bottom-width: 0;
    }

    .item.shortlist-options .action-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item.shortlist-options .action-wrapper .action {
        text-align: center;
    }

    .item.shortlist-options .action-wrapper .action:hover {
        cursor: pointer;
    }

    .item.shortlist-options .action-wrapper .action:first-of-type {
        margin-right: 20px;
    }

</style>
