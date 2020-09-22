<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">
        <div class="bl-label-16-style-2">
            Your Build Labour profile will mean you won't need to ever upload a resume or cover letter for our advertised jobs.
            So let's take some time to set up your profile correctly.
        </div>

        <div class="me-label">
            What is your current or most recent role/title?
        </div>
        <div class="emp-row">
            <!-- <div class="emp-form-label" v-if="input.most_recent_role">Most Recent Role</div> -->

            <input class="form-control" type="text" placeholder="Most Recent Role" v-model="input.most_recent_role" required
                @focus="hasFocus(true)"
                @keyup="onSearchJob(input.most_recent_role)" />

            <span class="err-msg" v-if="errors.most_recent_role">
                {{ errors.most_recent_role }}
            </span>
        </div>

        <div class="emp-row" style="margin-top:0" v-if="has_focus && job_roles && job_roles.length > 0">
            <ul class="list-group">
                <li class="list-group-item" v-for="(job, idx) in job_roles" :key="idx"
                    @click="onSelectJob(job)">

                    {{ job.job_role_name }}
                </li>
            </ul>
        </div>

        <div class="me-label" style="margin-bottom:17px">How many years of experience do you have in this role in total?</div>
        <div class="me-row">
            <div class="role-col-left">
                <input class="form-control" type="number" placeholder="Years" v-model="input.exp_year"
                    @focus="hasFocus(false)" />
            </div>

            <div class="role-col-right">
                <input class="form-control" type="number" placeholder="Months" v-model="input.exp_month"
                    @focus="hasFocus(false)" />
            </div>
        </div>
    </form>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "current-role",
        data() {
            return {
                has_focus: false,
                job_roles: [],
                input: {
                    most_recent_role: '', exp_year: '', exp_month: '',
                },
                errors: {
                    most_recent_role: '', exp_year: '', exp_month: '',
                },
                endpoints: {
                    save: '/api/v1/worker/current-role',
                },
            }
        },
        created() {
            let vm = this;

            Bus.$on('onboardingDetails', (workerDetail) => {

                if (workerDetail) {

                    this.input.most_recent_role = workerDetail.most_recent_role ? workerDetail.most_recent_role : '';
                    this.input.exp_year = workerDetail.exp_year ? workerDetail.exp_year : '';
                    this.input.exp_month = workerDetail.exp_month ? workerDetail.exp_month : '';
                }
            });

            Bus.$on('onboardingSubmitCurrentRole', function() {
                // if (! Utils.checkIfObjectIsEmpty(vm.input)) {
                    Api.submit(vm.endpoints.save, vm.$data.input, 'onboardingSubmitStatus');
                // }
            });

        },
        methods: {
            hasFocus(has_focus) {
                this.has_focus = has_focus;
            },
            onSearchJob(keyword) {
                this.job_roles = (keyword && keyword.length > 0) ? Api.getJobRoles(keyword) : [];
            },
            onSelectJob(job) {

                this.input.most_recent_role = job.job_role_name;

                this.job_roles = [];
            },
        },
    }
</script>
