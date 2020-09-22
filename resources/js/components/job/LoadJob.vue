<template>
    <div></div>
</template>

<script>
    export default {
        name: "load-job",
        data() {
            return {
                summary: {
                    id: '', photo_url: '', name: '', address: '', introduction: '',
                },
                job_details: {
                    title: '', description: '', about: '', exp_level: '', contract_type: '', salary: '', reports_to: '', location: '', min_exp_month: null, min_exp_year: null
                },
                requirements: [],
                responsibilities: [],
                endpoints: {
                    get: '/api/v1/company/',
                },
            }
        },
        props: {
            companyId: {
                type: String,
                default: null
            },
        },
        created() {
            let vm = this,
                companyId = this.companyId ? this.companyId : Utils.getUrlParams().cid;

            if (Utils.getUrlParams().jid && !Utils.getUrlParams().cache_id) {
                this.endpoints.get = this.endpoints.get + companyId + '/jobs/' + Utils.getUrlParams().jid;

            } else if(Utils.getUrlParams().cache_id) {

                this.endpoints.get = this.endpoints.get + companyId + '/job-preview/' + Utils.getUrlParams().cache_id

            } else {
                this.endpoints.get = this.endpoints.get + companyId;
            }

            this.getJob();
        },
        methods: {
            getJob() {
                let vm = this;

                axios.get(vm.endpoints.get, Utils.getBearerAuth())

                .then(function(response) {
                    if (Utils.getUrlParams().jid || Utils.getUrlParams().cache_id) {

                        let job = response.data.data.job;

                        if (job) {
                            if (job.company) {
                                Bus.$emit('profileAvatarDetails', Utils.getInitials(job.company.name));

                                vm.summary.id = job.company.id;
                                vm.summary.photo_url = job.company.photo_url;
                                vm.summary.name = job.company.name;
                                vm.summary.address = job.company.address;
                                vm.summary.introduction = job.company.introduction;
                            }

                            vm.job_details.id = Utils.getUrlParams().jid;
                            vm.job_details.job_role = job.job_role;
                            vm.job_details.title = job.title;
                            vm.job_details.template_name = job.template_name;
                            vm.job_details.is_template = job.is_template;
                            vm.job_details.status = job.status;
                            vm.job_details.description = job.description;
                            vm.job_details.about = job.about;
                            vm.job_details.project_size = job.project_size;
                            vm.job_details.exp_level = job.exp_level;
                            vm.job_details.contract_type = job.contract_type;
                            vm.job_details.salary = job.salary;
                            vm.job_details.salary_type = job.salary_type;
                            vm.job_details.reports_to = job.reports_to;
                            vm.job_details.location = job.location;
                            vm.job_details.min_exp_month = job.min_exp_month;
                            vm.job_details.min_exp_year = job.min_exp_year;

                        } else {

                            vm.job_details = null;
                        }

                        Bus.$emit('companySummaryDetails', vm.summary, 'view');
                        Bus.$emit('jobDetails', vm.job_details);
                        Bus.$emit('jobRequirementsDetails', job.requirements, vm.job_details);
                        Bus.$emit('jobResponsibilitiesDetails', job.responsibilities);
                        Bus.$emit('jobRequirementsMinExp', vm.job_details.min_exp_month, vm.job_details.min_exp_year);

                    } else {

                        let company = response.data.data.company;

                        vm.summary.id = company.id;
                        vm.summary.photo_url = company.photo_url;
                        vm.summary.name = company.name;
                        vm.summary.address = company.address;
                        vm.summary.introduction = company.introduction;

                        vm.job_details = null;

                        Bus.$emit('profileAvatarDetails', Utils.getInitials(company.name));
                        Bus.$emit('companySummaryDetails', vm.summary, 'new');

                        Bus.$emit('jobDetails', vm.job_details);

                    }

                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },
        }
    }
</script>
