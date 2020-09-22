<template>
    <div class="profile-item-2">
        <div class="profile-content">
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="23" viewBox="0 0 23 23">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h23v23H0z"/>
                        <path fill="#00aeef" fill-rule="nonzero" d="M10 3a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1h2a1 1 0 0 1 0 2H8a1 1 0 1 1 0-2h2zM6.525 4A1.25 1.25 0 0 0 7.75 5.5h7.5A1.25 1.25 0 0 0 16.475 4H17a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h.525zM6.5 7a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1h-1zm0 .5h1A.5.5 0 0 1 8 8v1a.5.5 0 0 1-.5.5h-1A.5.5 0 0 1 6 9V8a.5.5 0 0 1 .5-.5zm0 8.5a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1h-1zm0 .5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1A.5.5 0 0 1 6 18v-1a.5.5 0 0 1 .5-.5zm0-5a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1h-1zm0 .5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm4-4a.5.5 0 1 0 0 1h6a.5.5 0 1 0 0-1h-6zm0 4.5a.5.5 0 1 0 0 1h6a.5.5 0 1 0 0-1h-6zm0 4.5a.5.5 0 1 0 0 1h6a.5.5 0 1 0 0-1h-6z"/>
                    </g>
                </svg>

                Requirements
            </div>

            <div v-if="qualifications.length > 0">
                <div class="job-title">Qualifications</div>
                <div class="job-body">
                    <ul class="job-list-items">
                        <li v-for="(qualification, index) in qualifications" :key="index" v-if="qualifications.length > 0">
                            {{ qualification.qualification_level }} in {{ qualification.course_type }} or a related field
                        </li>
                    </ul>
                </div>
            </div>

            <div v-if="min_exp_detail.length > 0">
                <div class="job-title">Minimum Experience</div>
                <div class="job-body">
                    <!--<ul class="job-list-items">-->
                        <!--<li>Minimum of {{ min_exp }} years industry experience</li>-->
                        <!--<li v-for="(exp, index) in experience" :key="index">-->
                            <!--{{ exp }}-->
                        <!--</li>-->
                    <!--</ul>-->

                    <ul class="job-list-items">
                        <li>{{ min_exp_detail }}</li>
                    </ul>
                </div>
            </div>

            <div v-if="skills.length > 0">
                <div class="job-title">Skills</div>
                <div class="job-body">
                    <ul class="job-list-items">
                        <li v-for="(skill, index) in skills" :key="index" v-if="skill">
                            {{ skill }}
                        </li>
                    </ul>
                </div>
            </div>

            <div>
                    <span class="job-title">White Card Required</span>
                    <span class="col-sm-3" v-if="white_card === 1">
                        Yes
                    </span>
                    <span class="col-sm-3" v-else>
                        No
                    </span>
            </div>

            <div v-if="tickets.length > 0">
                <div class="job-title mt-4">Tickets</div>
                <div class="row">
                    <div class="col-md-6 col-sm-6" v-for="first in firstColumn" v-bind:key="first.id">
                        <span class="bl-label-15">
                            {{ first.ticket }}
                        </span>
                        <span class="bl-label-14">
                            {{ first.description }}
                        </span>
                    </div>

                    <div class="col-md-6 col-sm-6" v-for="second in secondColumn" v-bind:key="second.id">
                        <span class="bl-label-15">
                            {{ second.ticket }}
                        </span>
                        <span class="bl-label-14">
                            {{ second.description }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "job-requirements",
        data() {
            return {
                courses: [],
                keyword: '',
                has_whitecard: '',
                tickets: [],
                searchedTickets: [],
                selectedTicket: false,
                errors: {
                    ticket: ''
                },
                min_exp: '',
                qualifications: [],
                experience: [],
                skills: [],
                min_exp_detail: '',
                firstColumn: [],
                secondColumn: [],
                white_card: false
            }
        },
        created() {
            let vm = this;

            Bus.$on('jobRequirementsDetails', function(detailsArray) {

                if (detailsArray && detailsArray.length > 0) {

                    detailsArray.forEach((detail) => {
                        vm.white_card = detail.white_card;

                        if (!Utils.isNullOrEmpty(detail.title) && detail.items) {

                            if (detail.title === 'Tickets') {

                                vm.tickets = detail.items;
                            }

                            if (detail.title === 'Qualifications') {

                                vm.qualifications = detail.items;
                            }

                            if (detail.title === 'Skills') {

                                vm.skills = detail.items;
                            }
                        }
                    });

                    vm.display();
                }
            });

            Bus.$on('jobRequirementsMinExp', function(min_exp_month, min_exp_year) {

                vm.min_exp_detail = min_exp_month ? `${min_exp_month} month(s)` : '';

                vm.min_exp_detail += vm.min_exp_detail && min_exp_year ? ' and ' : '';

                vm.min_exp_detail += min_exp_year ? `${min_exp_year} year(s)` : '';

            });
        },
        methods: {
            display() {
                let len = this.tickets.length;
                let half = Math.ceil(len / 2);

                this.firstColumn = this.tickets.slice(0, half);
                this.secondColumn = this.tickets.slice(half, len);
            },
        },
    }
</script>
