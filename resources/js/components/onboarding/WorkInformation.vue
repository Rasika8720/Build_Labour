<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">

        <div class="me-label">Drivers Licence</div>
        <div class="me-row">
            <div class="me-col-mid">
                <div class="emp-form-label">State</div>
                <select v-model="input.drivers_license_state">
                    <option value="none">none</option>
                    <option value="ACT">ACT</option>
                    <option value="NSW">New South Wales</option>
                    <option value="QLD">Queensland</option>
                    <option value="SA">South Australia</option>
                    <option value="TAS">Tasmania</option>
                    <option value="VIC">Victoria</option>
                    <option value="WA">Western Australia</option>

                </select>
            </div>
            <div class="me-col-left">
                <div class="emp-form-label">Type</div>
                <select v-model="input.drivers_license_type">
                    <option value="none">none</option>
                    <option value="Full">Full</option>
                    <option value="Probationary">Probationary</option>
                </select>
            </div>
            <div class="me-col-right" v-if="input.drivers_license_type != 'none' && input.drivers_license_state != 'none'">
                <div class="emp-form-label">I Can Drive</div>
                <select v-model="input.can_drive_manual">
                    <option value="0">Automatic</option>
                    <option value="1">Manual</option>
                </select>
            </div>
        </div>

        <div class="skill-label">
            I have the right to legally work in Australia
        </div>
        <div class="bl-inline">
            <input id="right_to_work_1" class="styled-checkbox-round" type="checkbox"
                ref="right_to_work_1" @change="formatCheckbox('right_to_work', 1)" />
            <label for="right_to_work_1">Yes</label>

            <input id="right_to_work_0" class="styled-checkbox-round" type="checkbox"
                ref="right_to_work_0" @change="formatCheckbox('right_to_work', 0)" />
            <label for="right_to_work_0">No</label>
        </div>
        <div class="me-label-2 text-danger" style="margin-top: 10px;" v-show="input.right_to_work == 0">
            Build Labour Adheres to Australian Fair Work Requirements <a href="https://www.australia.gov.au/information-and-services/immigration-and-visas/work-visas" target="_blank">See legal requirements</a>
        </div>

        <!--<div class="me-label">-->
            <!--I have an Australian Tax File Number (TFN)-->
        <!--</div>-->
        <!--<div class="bl-inline">-->
            <!--<input id="has_tfn_1" class="styled-checkbox-round" type="checkbox"-->
                <!--ref="has_tfn_1" @change="formatCheckbox('has_tfn', 1)" />-->
            <!--<label for="has_tfn_1">Yes</label>-->
            <!---->
            <!--<input id="has_tfn_0" class="styled-checkbox-round" type="checkbox"-->
                <!--ref="has_tfn_0" @change="formatCheckbox('has_tfn', 0)" />-->
            <!--<label for="has_tfn_0">No</label>-->
        <!--</div>-->

        <div class="me-label">
            I have an Australian Business Number (ABN)
        </div>
        <div class="bl-inline">
            <input id="has_abn_1" class="styled-checkbox-round" type="checkbox"
                ref="has_abn_1" @change="formatCheckbox('has_abn', 1)" />
            <label for="has_abn_1">Yes</label>

            <input id="has_abn_0" class="styled-checkbox-round" type="checkbox"
                ref="has_abn_0" @change="formatCheckbox('has_abn', 0)" />
            <label for="has_abn_0">No</label>
        </div>

        <div class="me-label">
            I am competent in WRITTEN and SPOKEN english
        </div>
        <div class="bl-inline">
            <input id="english_skill_1" class="styled-checkbox-round" type="checkbox"
                ref="english_skill_1" @change="formatCheckbox('english_skill', 1)" />
            <label for="english_skill_1">Yes</label>

            <input id="english_skill_0" class="styled-checkbox-round" type="checkbox"
                ref="english_skill_0" @change="formatCheckbox('english_skill', 0)" />
            <label for="english_skill_0">No</label>
        </div>

        <div class="me-row" v-show="input.english_skill == 1 || input.english_skill == 0">
            <div class="me-col-mid">
                <div class="emp-form-label">Level</div>
                <select v-model="input.english_skill_competency">
                    <option value="native">Native</option>
                    <option value="competent">Competent</option>
                </select>
            </div>
        </div>

        <!--<div class="me-label">-->
            <!--I have a valid driver's licence-->
        <!--</div>-->
        <!--<div class="bl-inline">-->
            <!--<input id="drivers_license_1" class="styled-checkbox-round" type="checkbox"-->
                <!--ref="drivers_license_1" @change="formatCheckbox('drivers_license', 1)" />-->
            <!--<label for="drivers_license_1">Yes</label>-->
            <!---->
            <!--<input id="drivers_license_0" class="styled-checkbox-round" type="checkbox"-->
                <!--ref="drivers_license_0" @change="formatCheckbox('drivers_license', 0)" />-->
            <!--<label for="drivers_license_0">No</label>-->
        <!--</div>-->

        <div class="me-label">
            I own/have access to a registered vehicle on a permanent basis
        </div>
        <div class="bl-inline">
            <input id="has_registered_vehicle_1" class="styled-checkbox-round" type="checkbox"
                ref="has_registered_vehicle_1" @change="formatCheckbox('has_registered_vehicle', 1)" />
            <label for="has_registered_vehicle_1">Yes</label>

            <input id="has_registered_vehicle_0" class="styled-checkbox-round" type="checkbox"
                ref="has_registered_vehicle_0" @change="formatCheckbox('has_registered_vehicle', 0)" />
            <label for="has_registered_vehicle_0">No</label>
        </div>

        <div class="me-label-2" v-show="input.has_registered_vehicle == 0">
            Note: Some jobs may require the use of your own registered vehicle.
        </div>
    </form>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "work-information",
        data() {
            return {
                input: {
                    right_to_work: '', has_tfn: '', has_abn: '', day_labour_status: '', night_shift: '',
                    english_skill: '', drivers_license: '', drivers_license_state: '', drivers_license_type: '', has_registered_vehicle: '',
                    australian_tfn: '', english_skill_competency: '', can_drive_manual: ''
                },
                errors: {
                    right_to_work: '', has_tfn: '', has_abn: '',
                    english_skill: '', drivers_license: '', has_registered_vehicle: '',
                },
                endpoints: {
                    save: '/api/v1/worker/affirmations',
                },
            }
        },

        created() {
            let vm = this;

            Bus.$on('aboutMeTechnicalDetails', function(details) {

                if (details) {

                    console.log(details);

                    vm.input.day_labour_status = false;
                    vm.input.night_shift = false;
                    vm.input.right_to_work = details.right_to_work;
                    vm.input.has_tfn = details.has_tfn;
                    vm.input.has_abn = details.has_abn;
                    vm.input.english_skill = details.english_skill;
                    vm.input.english_skill_competency = details.english_skill_competency;
                    vm.input.drivers_license = details.drivers_license;
                    vm.input.drivers_license_state = details.drivers_license_state ? details.drivers_license_state : '';
                    vm.input.drivers_license_type = details.drivers_license_type ? details.drivers_license_type : '';
                    vm.input.can_drive_manual = details.can_drive_manual ? 1 : 0;
                    vm.input.australian_tfn = details.australian_tfn ? details.australian_tfn : '';
                    vm.input.has_registered_vehicle = details.has_registered_vehicle;

                    vm.formatCheckbox('right_to_work', details.right_to_work);
                //    vm.formatCheckbox('has_tfn', details.has_tfn);
                    vm.formatCheckbox('has_abn', details.has_abn);
                    vm.formatCheckbox('english_skill', details.english_skill);
                  //  vm.formatCheckbox('drivers_license', details.drivers_license);
                    vm.formatCheckbox('has_registered_vehicle', details.has_registered_vehicle);
                }
            });

            Bus.$on('onboardingSubmitWorkInformation', function(action) {
                if (action == 'clear') {
                    Utils.setObjectValues(vm.input, null);
                }

                if(vm.$data.input.drivers_license_type == 'none' || vm.$data.input.drivers_license_state == 'none')
                    vm.$data.input.can_drive_manual = 0;

                Api.submit(vm.endpoints.save, vm.$data.input, 'onboardingSubmitStatus');

                Bus.$emit('aboutMeTechnicalDetails', vm.input);
            });
        },

        methods: {

            formatCheckbox(refName, value) {
                Utils.formatCheckbox(this.$refs, this.input, refName, value);
            },
            clickFocus(e)
            {
                e.target.scrollIntoView();
            }

        }
    }
</script>
