<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">

        <div class="bl-label-16-style-2">
            Enter any previous experience you have had in construction related roles:
        </div>

        <div class="skill-label">
            I have worked in the following areas of construction
        </div>

        <div class="sector-wrapper">
            <div class="bl-inline" v-for="(sector, index) in sectors" :key="index">
                <input :id="'sector-styled-checkbox-'+index" class="styled-checkbox" type="checkbox"
                       :value="sector.id" v-model="input.sectors" />
                <label :for="'sector-styled-checkbox-'+index" style="width:125px">{{ sector.business_type }}</label>
            </div>
        </div>

        <div class="skill-label">
            In the following Tiers <img src="/img/icons/info.png" srcset="/img/icons/info@2x.png 2x, /img/icons/info@3x.png 3x" class="info" data-target="#modalTier" data-toggle="modal" data-backdrop="static"
                data-keyboard="false">
        </div>
        <div class="bl-inline" v-for="(tier, index) in tiers" :key="index + sectors.length">
            <input :id="'tier-styled-checkbox-'+index" class="styled-checkbox" type="checkbox"
                :value="tier.id" v-model="input.tiers" />

            <label :for="'tier-styled-checkbox-'+index">{{ tier.tier_name }}</label>
        </div>

        <hr>

        <button type="button"
            data-toggle="modal"
            data-backdrop="static"
            data-keyboard="false"
            data-target="#modalEmployment"
            @click="action(-1)">

            + Add to employment history
        </button>

        <!-- <div class="btn btn-link btn-delete mt-3"
            data-toggle="modal"
            data-backdrop="static"
            data-keyboard="false"
            data-target="#modalEmployment"
            @click="action(-1)">

            Add new position
        </div> -->

        <employment-list></employment-list>
    </form>
</template>

<script>
    import Api from '@/api';
    import EmploymentList from '../profile/EmploymentList';

    export default {
        name: "employment-history",
        data() {
            return {
                disabled: false,
                sectors: [
                    { id: 1, business_type: 'Residential' },
                    { id: 2, business_type: 'Commercial' },
                    { id: 3, business_type: 'Civil' },
                    { id: 4, business_type: 'Industrial' }
                ],
                tiers: [
                    { id: 1, tier_name: 'Tier 1' },
                    { id: 2, tier_name: 'Tier 2' },
                    { id: 3, tier_name: 'Tier 3' },
                    { id: 4, tier_name: 'Tier 4' },
                ],
                input: {
                    tiers: [], sectors: [],
                },
                endpoints: {
                    save: '/api/v1/worker/sectors',
                },
            }
        },
        created() {
            let vm = this;

            Bus.$on('onboardingDetails', (workerDetail) => {

                if (workerDetail) {

                    if (workerDetail.sectors) {

                        workerDetail.sectors.forEach((sector) => {

                            vm.input.sectors.push(sector.pivot.business_type_id);
                        });
                    }

                    if (workerDetail.tiers) {

                        workerDetail.tiers.forEach((tier) => {

                            vm.input.tiers.push(tier.pivot.tier_id);
                        });
                    }
                }
            });

            Bus.$on('onboardingSubmitEmploymentHistory', function(saveInput) {
                Api.submit(vm.endpoints.save, saveInput ? saveInput : vm.$data.input, 'onboardingSubmitStatus');
            });
        },
        methods: {
            action(index) {
                Bus.$emit('showEmployment', index, null);
            },
            open(){

            }
        },
        components: {
            EmploymentList
        },
    }
</script>

<style Scoped>
    hr {
        border-top: 1px solid #f9f9f9;
    }
    button {
        width: 250px;
        background-image: none;
        background-color: #005778;
        margin-top: 0;
    }
    .info {
        width: 20px;
        height: 20px;
        object-fit: contain;
    }
    .info:hover{
        cursor: pointer;
    }
</style>
