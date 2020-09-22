<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">
        <div class="skill-label">
            How far are you willing to travel from home for employment?
        </div>
        <div class="emp-row-2 row-center">
            <div class="emp-col-left-2">
                <div class="bl-slider">
                    <input type="range" min="0" max="500" step="5" class="slider" :style="maxDistance" v-model="input.max_distance">
                </div>
            </div>
            <div class="emp-col-right-2">
                <label>
                    {{ input.max_distance }} km
                </label>
            </div>
        </div>

        <div class="skill-label">
            Would you work/relocate to another state? If Yes, tick states that apply.
        </div>
        <div class="skill-label-2">
            Relocation may be at own expense.
        </div>

        <div class="bl-inline" v-for="(state, index) in states" :key="index">
            <input class="styled-checkbox" :id="'styled-checkbox-'+index" type="checkbox"
                v-bind:value="state" v-model="input.selected" />
            <label :for="'styled-checkbox-'+index">{{ state }}</label>
        </div>

        <div class="skill-label">
            What is your ideal next role?
        </div>
        <textarea rows="3" ref="idealIntro" class="form-control" style="overflow:hidden"
            placeholder="Example: My ideal next role would be as a qualified plumber working on high-end residential jobs with an awesome team."
            @focus="textAreaAdjust('idealIntro')"
            @keyup="textAreaAdjust('idealIntro')"
                  @click="clickFocus"
            v-model="input.introduction">
        </textarea>

        <div class="me-label">
            Ideally this would happen in:
        </div>
        <div class="me-row">
            <div class="role-col-left">
                <select v-model="input.when">
                    <option key="0" value="0">Now</option>
                    <option key="1" value="3">3 months</option>
                    <option key="2" value="6">6 months</option>
                    <option key="3" value="9">9 months</option>
                    <option key="4" value="12">12+ months</option>
                    <option key="5" value="">Rather not say</option>
                </select>
                <span class="err-msg" v-if="errors.when">
                    {{ errors.when }}
                </span>
            </div>
        </div>

        <div class="me-label">
            Would you relocate overseas?
        </div>
        <div class="bl-inline">
            <input id="willing_to_relocate_1" class="styled-checkbox-round" type="checkbox"
                ref="willing_to_relocate_1" @change="formatCheckbox('willing_to_relocate', 1)" />
            <label for="willing_to_relocate_1">Yes</label>

            <input id="willing_to_relocate_0" class="styled-checkbox-round" type="checkbox"
                ref="willing_to_relocate_0" @change="formatCheckbox('willing_to_relocate', 0)" />
            <label for="willing_to_relocate_0">No</label>
        </div>

        <div class="me-label" v-if="input.willing_to_relocate">Select up to three countries:</div>
        <div class="me-row mb-3" v-if="input.willing_to_relocate">
            <div class="role-col-left">
                <select v-model="input.selected_countries[0]" class="mb-3" style="width:140%">
                    <option v-for="(country, index) in countries" :key="index" :value="country">{{ country }}</option>
                </select>

                <select v-model="input.selected_countries[1]" class="mb-3" style="width:140%">
                    <option v-for="(country, index) in countries" :key="index" :value="country">{{ country }}</option>
                </select>

                <select v-model="input.selected_countries[2]" class="mb-3" style="width:140%">
                    <option v-for="(country, index) in countries" :key="index" :value="country">{{ country }}</option>
                </select>
            </div>
        </div>
    </form>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "work-preferences",
        data() {
            return {
                countries: [],
                states: [
                    'QLD', 'NSW', 'SA', 'VIC', 'WA', 'ACT', 'TAS', 'NT',
                ],
                input: {
                    max_distance: 0, state: '', selected: [], introduction: '', when: '',
                    willing_to_relocate: '', countries: '', selected_countries: [],
                },
                errors: {
                    max_distance: '', selected: '', introduction: '', when: '',
                    willing_to_relocate: '', countries: '',
                },
                endpoints: {
                    save: '/api/v1/worker/next-role',
                },
            }
        },

        created() {
            let vm = this;

            Bus.$on('idealRoleDetails', function(details) {
                if (details) {
                    vm.input.introduction = details.introduction;
                    vm.input.when = details.when;
                    vm.input.max_distance = details.max_distance && details.max_distance != '' ? details.max_distance : 0;
                    vm.input.willing_to_relocate = details.willing_to_relocate;
                    vm.input.state = details.state;
                    vm.input.countries = details.countries;
                    vm.input.selected = vm.input.state ? vm.input.state.split(',') : [];
                    vm.input.selected_countries = vm.input.countries ? vm.input.countries.split(',') : [];

                    vm.formatCheckbox('willing_to_relocate', details.willing_to_relocate);
                }
            });

            Bus.$on('onboardingSubmitWorkPreferences', function(action) {
                if (action == 'clear') {
                    Utils.setObjectValues(vm.input, null);

                } else {
                    vm.input.state = vm.input.selected.toString();
                    vm.input.countries = vm.input.selected_countries.length > 0 ?
                                                vm.input.selected_countries.toString() : null;
                }

                Api.submit(vm.endpoints.save, vm.$data.input, 'onboardingSubmitStatus');

                Bus.$emit('idealRoleDetails', vm.input);
            });

            this.getCountries();
        },

        computed: {
            maxDistance() {
                return {
                    background: `linear-gradient(to right, #00aeef ${(this.input.max_distance / 500) * 100}%,
                                #00aeef ${(this.input.max_distance / 500) * 100}%, #fff 00%, #fff 100%)`
                }
            },
        },

        methods: {

            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },

            formatCheckbox(refName, value) {
                Utils.formatCheckbox(this.$refs, this.input, refName, value);

                if (! value) {
                    this.input.selected_countries = [];
                }
            },

            getCountries() {
                let vm = this;

                Promise.resolve(Api.getCountries()).then(function(data) {
                    vm.countries = data.data.countries;
                });
            },
            clickFocus(e)
            {
                e.target.scrollIntoView();
            }

        }
    }
</script>
