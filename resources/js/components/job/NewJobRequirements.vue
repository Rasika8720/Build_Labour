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

            <form>
                <div class="form-group">
                    <!-- Qualifications -->
                    <div class="job-title">Qualifications</div>

                    <div class="form-group emp-row row-center" v-for="(to, index) in qualifications" :key="'quaItem' + index">

                        <div class="job-col-left">
                            <input class="form-control" type="text" placeholder="Course Type" v-model="qualifications[index].course_type"
                                   @focus="onFocus('qualifications')"
                                   @keyup="onSearchCourse(qualifications[index].course_type, index)" />
                        </div>

                        <div class="job-col-right">
                            <span @click="removeEntity(index, 'qualifications')">
                                <img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x" style="cursor: pointer;">
                            </span>
                        </div>

                        <div class="job-col-left" style="margin-top: 0; margin-left: -15px" v-if="activeIndex == index && courses && courses.length > 0 && focusTo=='qualifications'">
                            <ul class="list-group">
                                <li class="list-group-item" v-for="(course, idx) in courses" :key="idx"
                                    @click="onSelectCourse(course)">

                                    {{ course.course_name }}
                                </li>
                            </ul>
                        </div>

                        <div class="me-row mt-4">
                            <select v-model="qualifications[index].qualification_level" @focus="onFocus('selectQualifications')">
                                <option key="1" value="Cert I">Cert I</option>
                                <option key="2" value="Cert II">Cert II</option>
                                <option key="3" value="Cert III">Cert III</option>
                                <option key="4" value="Cert IV">Cert IV</option>
                                <option key="5" value="Diploma">Diploma</option>
                                <option key="6" value="Advanced Diploma">Advanced Diploma</option>
                                <option key="7" value="Bachelor's Degree">Bachelor</option>
                                <option key="8" value="Master's Degree">Masters</option>
                                <option key="9" value="Doctor's Degree">Doctorate</option>
                            </select>
                        </div>
                    </div>

                    <div class="btn btn-link btn-delete"  @click="addEntity('qualifications')">Add new qualification</div>

                    <!-- Experience -->
                    <div class="job-title">Minimum Experience</div>

                     <!--<div class="me-row mt-4 mb-5">-->
                        <!--<input class="form-control" type="text" placeholder="Minimum Experience" v-model="min_exp"/>-->
                    <!--</div>-->

                    <div class="form-group emp-row row-center">
                        <div class="role-col-left">
                            <div class="emp-form-label">No of Month</div>
                            <select v-model="min_exp_month" @focus="onFocus('no_of_month')">
                                <option v-for="month in months" :key="month.id" v-bind:value="month.id"> {{ ("0" + month.id).slice(-2) }}</option>
                            </select>
                            <span class="err-msg" v-if="errors.end_month">
                            {{ errors.end_month }}
                        </span>
                        </div>

                        <div class="role-col-right">
                            <div class="emp-form-label">No of Year</div>
                            <select v-model="min_exp_year" @focus="onFocus('no_of_years')">
                                <option v-for="(year, index) in years" :key="index" v-bind:value="year + 1">{{ year + 1 }}</option>
                            </select>
                            <span class="err-msg" v-if="errors.end_year">
                                {{ errors.end_year }}
                            </span>
                        </div>
                    </div>
                    <!--<div class="form-group emp-row row-center" v-for="(to, index) in experience" :key="'expItem-' + index">-->
                        <!--<div class="job-col-left">-->
                            <!--<input class="form-control" type="text" placeholder="Start typing..." v-model="experience[index]" />-->
                        <!--</div>-->

                        <!--<div class="job-col-right">-->
                            <!--<span @click="removeEntity(index, 'experience')">-->
                                <!--<img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x" style="cursor: pointer;">-->
                            <!--</span>-->
                        <!--</div>-->
                    <!--</div>-->

                    <!--<div class="btn btn-link btn-delete"  @click="addEntity('experience')">Add another</div>-->

                    <!-- Skills -->
                    <div class="job-title">
                        Skills

                        <span class="err-msg emp-row mt-2 mb-0" v-if="errors.skills">
                            {{ errors.skills }}
                        </span>
                    </div>

                    <div class="form-group emp-row row-center" v-for="(to, index) in skills" :key="'skItem-' + index">

                        <div class="job-col-left">
                            <input class="form-control" type="text" placeholder="Start typing..." v-model="skills[index]" @focus="onFocus('skills')"/>
                        </div>

                        <div class="job-col-right">
                            <span @click="removeEntity(index, 'skills')">
                                <img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x" style="cursor: pointer;">
                            </span>
                        </div>
                    </div>

                    <div class="btn btn-link btn-delete"  @click="addEntity('skills')">Add another</div>

                    <!-- Tickets -->
                    <div class="job-title">Tickets</div>

                    <div class="emp-row">
                        <div class="ticket-col-left">
                            <input class="form-control" type="text"  placeholder="Type to add in tickets..." v-model="keyword" @focus="onFocus('tickets')" @keyup="onSearchTicket(keyword)" />
                        </div>
                        <div class="ticket-col-right">
                            <button style="margin-left: 0px; width: 100%;" class="add-button" type="button" @click="onAddTicket()">Add</button>
                        </div>
                        <span class="err-msg" v-if="errors.ticket">
                            {{ errors.ticket }}
                        </span>
                    </div>

                    <div class="emp-row" style="margin-top:0" v-if="searchedTickets.length > 0 && focusTo == 'tickets'">
                        <ul class="list-group">
                            <li class="list-group-item" v-for="(ticket, idx) in searchedTickets" :key="idx"
                                @click="onSelectTicket(ticket)">
                                {{ ticket.ticket }} - {{ ticket.description }}
                            </li>
                        </ul>
                    </div>

                    <div class="emp-row" v-for="(ticket, idx) in tickets" :key="'tkItem' + idx" v-if="tickets.length > 0">
                        <span class="ticket-label">{{ ticket.ticket }} - {{ ticket.description }}</span>

                        <span class="remove-ticket-icon" @click="onRemoveTicket(idx)">
                            <img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x">
                        </span>
                    </div>

                    <div class="emp-row">
                        <label for="whiteCard">
                            <input type="checkbox"
                                   id="whiteCard"
                                   name="white_card"
                                   v-model="white_card">
                            White Card is compulsory
                        </label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "new-job-requirements",
        data() {
            return {
                show: false,
                activeIndex: 0,
                courses: [],
                keyword: '',
                min_exp: '',
                min_exp_month: null,
                min_exp_year: null,
                white_card: '',
                tickets: [],
                searchedTickets: [],
                selected: false,
                errors: {
                    ticket: '',
                    skill: ''
                },
                qualifications: [],
                experience: [],
                skills: [],
                months: Utils.getMonths(),
                years: Utils.getYearsJobRequirements(),
                focusTo: null
            }
        },
        created() {
            let vm = this;

            Bus.$on('newJobDetails', function() {
                Bus.$emit('newJobRequirements', {
                    qualifications: vm.qualifications,
                    experience: vm.experience,
                    skills: vm.skills,
                    tickets: vm.tickets,
                    white_card: vm.white_card,
                    //min_exp: vm.min_exp,
                    min_exp_month: vm.min_exp_month,
                    min_exp_year: vm.min_exp_year
                });
            });

            Bus.$on('newJobRequirementsError', function(errors) {

                vm.errors = errors;
            });

            Bus.$on('jobRequirementsDetails', function(detailsArray, jobDetails) {

                if (detailsArray && detailsArray.length != 0) {

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
                        console.log(vm.white_card);
                    });

                    vm.min_exp_month = jobDetails.min_exp_month ? jobDetails.min_exp_month : null;
                    vm.min_exp_year = jobDetails.min_exp_year ? jobDetails.min_exp_year : null;

                    if (! vm.qualifications || vm.qualifications.length <= 0) vm.qualifications.push({ course_type: '', qualification_level: '' });
                    if (! vm.experience || vm.experience.length <= 0 ) vm.experience.push('');
                    if (! vm.skills || vm.skills.length.length <= 0) vm.skills.push('');
                }
            });

            Bus.$on('clearNewJobRequirements', () => {

                this.courses = [];
                this.searchedTickets = [];
            });

            this.qualifications.push({ course_type: '', qualification_level: '' });
            this.experience.push('');
            this.skills.push('');
        },
        methods: {
            onFocus(type) {

                this.focusTo = type;

                this.courses = [];
                this.searchedTickets = [];

                Bus.$emit('clearNewJobDetails');
                Bus.$emit('clearNewJobResponsibilities');
            },
            onSearchCourse(keyword, index) {
                if (! keyword) return;

                this.courses = Api.getCourses(keyword);

                this.activeIndex = index;
            },
            onSelectCourse(course) {
                this.qualifications[this.activeIndex].course_type = course.course_name;

                this.courses = [];
            },
            addEntity(field) {
                switch(field) {
                    case 'qualifications':
                        this[field] = this[field].filter(r => r.course_type !== '' && r.qualification_level !== '');
                        this[field].push({ course_type: '', qualification_level: '' });
                        break;

                    case 'experience':
                    case 'skills':
                        this[field] = this[field].filter(r => r !== '');
                        this[field].push('');
                        break;
                }
            },
            removeEntity(index, field) {

                if (this[field].length <= 1) {

                    switch(field) {

                        case 'qualifications':

                            if (this[field][index].course_type) {
                                this[field][index].course_type = '';
                            }

                            if (this[field][index].qualification_level) {
                                this[field][index].qualification_level = '';
                            }

                            break;

                        case 'experience':
                        case 'skills':
                            this[field] = this[field].filter(r => r !== '');
                            this[field].push('');
                            break;
                    }

                }

                if (this[field].length > 1) {
                    this[field].splice(index, 1);
                }
            },
            onSearchTicket(keyword) {
                if (! keyword) return;

                this.searchedTickets = Api.getTickets(keyword);
            },
            onSelectTicket(ticket) {
                this.selected = ticket;
                this.keyword = ticket.ticket + ' - ' + ticket.description;
                this.searchedTickets = [];
            },
            onRemoveTicket(index) {

                this.tickets.splice(index, 1);
            },
            onAddTicket() {
                if (! this.selected) {
                    this.keyword = this.keyword.trim();

                    let description = this.keyword.substr(this.keyword.indexOf(' ') + 1);

                    this.selected = {
                        id: null,
                        ticket: this.keyword.split(' ')[0],
                        description: description ? description.replace(/^[^a-zA-Z]+/, '') : null,
                    }
                }

                if (! this.tickets.find(el => (this.selected.id && el.id === this.selected.id) ||
                                                (! this.selected.id && (el.ticket == this.selected.ticket)
                                                    && (el.description == this.selected.description)))) {

                    if (this.selected.ticket.length > 0) {

                        this.tickets.unshift(this.selected);
                        this.keyword = '';
                        this.errors.ticket = '';
                        this.has_focus = false;
                    }

                } else {

                    this.errors.ticket = 'Ticket already exists on selected list';
                }

                this.selected = false;
            },
        },
    }
</script>

<style scoped>
    .job-title {
        margin-bottom: -12px;
    }
</style>
