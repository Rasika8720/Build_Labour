<template>
    <div class="profile-item-2">
        <div class="profile-content" style="overflow: visible;">
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h24v24H0z"></path>
                        <path fill="#00aeef" d="M7 5V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2
                        2v7a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4zm1.4 0h7.2v-.9a.7.7 0 0
                        0-.7-.7H9.1a.7.7 0 0 0-.7.7V5zM19 17h2c.818 0 1.544-.393 2-1v4a2 2 0 0 1-2 2H3a2
                        2 0 0 1-2-2v-4c.456.607 1.182 1 2 1h2v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1h6v1a1
                        1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1zm-1 0v1h-2v-1h2zM6 17h2v1H6v-1z"></path>
                    </g>
                </svg>
                Job Details
            </div>
            <form>
                <div class="form-group">
                    <div class="job-title mb-2">Job Title</div>

                    <input type="text" class="form-control" style="margin: 0; max-width: 524px;" placeholder=""
                           v-model="input.title"
                           @focus="onFocus('job_title')"
                           @blur="onLeave()"/>

                    <span class="err-msg" v-if="errors.title">
                        {{ errors.title }}
                    </span>
                </div>

                <div class="form-group">
                    <div class="job-title">Job Description</div>

                    <textarea rows="3" ref="jobDesc" class="form-control" style="overflow: hidden;"
                              placeholder="Example: The Project Manager is accountable for the leadership and management of their nominated project including the achievement of safety, quality, commercial and programme objectives and the effective day to day management of the project team."
                              @focus="textAreaAdjust('jobDesc')"
                              @keyup="textAreaAdjust('jobDesc')"
                              v-model="input.description">
                    </textarea>

                    <span class="err-msg" v-if="errors.description">
                        {{ errors.description }}
                    </span>
                </div>

                <div class="form-group">
                    <div class="job-title">Job Location</div>

                    <input type="text" class="form-control" ref="jobLocation" placeholder="Start typing address..."
                           v-model="input.location"
                           @focus="onFocus('locations')"
                           @keyup="onChangeLocation(input.location)"
                           @blur="onLeave()">

                    <span class="err-msg" v-if="errors.location">
                        {{ errors.location }}
                    </span>
                </div>

                <div class="emp-row" style="margin-top:0" v-if="locations && locations.length > 0 && focusTo == 'locations'">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(place, idx) in locations" :key="idx"
                            @click="onSelectLocation(place.place_name)">
                            {{ place.place_name }}
                        </li>
                    </ul>
                </div>

                <div class="form-group">
                    <div class="job-title">Pay Rate (Per Hour)</div>

                    <input type="text" ref="payRate" class="form-control" style="overflow: hidden;"
                           placeholder="$ Enter amount"
                           pattern="^\$\d{1,3}(,\d{3})*(\.\d+)?$"
                           @keyup="formatCurrency('pay_rate', $event)"
                           @blur="formatCurrency('pay_rate', $event, 'blur')"
                           @focus="onFocus('pay_rate')"
                            v-model="input.pay_rate">

                    <span class="err-msg" v-if="errors.pay_rate">
                        {{ errors.pay_rate }}
                    </span>
                </div>

                <div class="form-group">
                    <div class="job-title">Job Date</div>

                    <datepicker format="dd/MM/yyyy"
                                v-model="input.job_date"
                                :disabled-dates="disabledDates"
                                ref="jobDate"
                                @focus="onFocus('jobDate')"></datepicker>
                </div>

                <div class="form-group">
                    <div class="job-title">Shift Times</div>

                    <div class="emp-row">
                        <label class="emp-form-label">Start</label>
                        <select v-model="input.shift_start" @focus="onFocus('shift_start')">
                            <option v-for="time in times" :value="time"> {{ time}}</option>
                        </select>
                    </div>

                    <div class="emp-row">
                        <label class="emp-form-label">End</label>
                        <select v-model="input.shift_end">
                            <option v-for="time in times" :value="time"> {{ time}}</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import Datepicker from "vuejs-datepicker";

    export default {
        name: 'new-day-labour-details',
        components: {
            Datepicker
        },
        data() {
            return {
                disabledDates: {
                    to: new Date()
                },
                focusTo: null,
                errors: {
                    title: '',
                    description: '',
                    pay_rate: ''
                },
                input: {
                    description: '',
                    title: '',
                    location: '',
                    pay_rate: '',
                    job_date: '',
                    shift_start: '',
                    shift_end: '',
                },
                times: Utils.createDayTimeIncrements(4),
                locations: []
            }
        },
        mounted() {
            let vm = this;
            Bus.$emit('activateTab', 'jobs');
            Bus.$on('newDayLabourSave', this.onSave);

            Bus.$on('newDayLabourDetailsErrors', (error) => {
                vm.errors = error;
            });
        },
        methods: {
            onLeave(type) {

                if (this.leaveTimeoutHandler) {

                    clearTimeout(this.leaveTimeoutHandler)
                }

                this.leaveTimeoutHandler = setTimeout(() => {

                    this.reports_to_job_roles = [];
                    this.location = [];

                }, 2000)

            },
            onFocus(type) {
                this.focusTo = type;
            },
            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },
            formatCurrency(field, e, blur = null) {
                this.input[field] = Utils.formatCurrency(e, blur);
            },
            onSave() {
                Bus.$emit('newDayLabourDetails', this.input);
            },
            async onChangeLocation(keyword) {

                let vm = this;

                if (this.timeoutHandler) {

                    clearTimeout(this.timeoutHandler);
                }

                if (keyword && keyword.length > 0) {

                    this.timeoutHandler = await setTimeout(() => {

                        Promise.resolve(Api.getLocationsPromise(keyword)).then((data) => {
                            console.log(data.data);
                            this.locations = (data.data && data.data.locations) ? data.data.locations.features : [];
                        });
                    }, 400);

                } else {

                    this.locations = [];
                }

            },
            onSelectLocation(location) {
                this.input.location = location;
                this.locations = [];
            },
        }
    }
</script>

<style>
    .vdp-datepicker input {
        width: 100%;
    }
</style>
