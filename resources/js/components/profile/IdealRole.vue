<template>
    <div class="profile-item-1" v-if="editable">
        <div class="profile-content">
            <div class="profile-content-p20 pt-3 pb-4">
                
                <main-modal id="modalIdealRole">
            
                    <template slot="custom-modal-title">
                        <h4 class="modal-title">Edit Ideal Next Role</h4>
                        <div class="close" data-dismiss="modal" @click="close">&times;</div>
                    </template>

                    <template slot="custom-modal-content">

                        <work-preferences></work-preferences>
                        
                    </template>

                    <template slot="custom-modal-footer">
                        <div class="btn btn-link btn-delete" data-dismiss="modal" @click="deleteRecord"
                            v-if="introduction || when || max_distance || state || countries || 
                                  selected.length != 0 || selected_countries.length != 0">
                            Delete
                        </div>

                        <button class="pull-right" type="submit" @click="submit">
                            Save Changes
                        </button>
                    </template>

                </main-modal>

                <edit-icon cls="edit-icon" data-target="#modalIdealRole"></edit-icon>

                <div class="profile-title">Your Ideal Next Role</div>        
                <span class="bl-label-14">(Visible only to you)</span>
                
                <div v-if="introduction">
                    <span class="profile-intro">
                        {{ introduction }}
                    </span>
                </div>

                <div v-if="when">
                    <span class="bl-label-15">When</span>
                    <span class="bl-label-14">
                        {{ formatWhen(when) }} ({{ formatWhenMonth(when) }})
                    </span>
                </div>

                <div v-if="max_distance && max_distance != 0">
                    <span class="bl-label-15">Maximum Distance from home</span>
                    <span class="bl-label-14">
                        {{ max_distance }}km
                    </span>
                </div>

                <div v-if="state">
                    <span class="bl-label-15">Willing to relocate to</span>
                    <span v-for="(s, index) in selected" :key="index" class="bl-label-14">
                        {{ s }}
                    </span>
                </div>

                <div v-if="countries">
                    <span class="bl-label-15">Willing to relocate overseas</span>
                    <span v-for="(c, index) in selected_countries" :key="index" class="bl-label-14">
                        {{ c }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import MainModal from '../common/MainModal';
    import WorkPreferences from '../onboarding/WorkPreferences';
    import EditIcon from '../common/EditIcon';
    
    export default {
        name: "ideal-role",
        data() {
            return {
                editable: false,
                introduction: '',
                when: '',
                max_distance: '',
                state: '',
                countries: '',
                selected: [],
                selected_countries: [],
                states: [
                    'QLD', 'NSW', 'SA', 'VIC', 'WA', 'ACT', 'TAS', 'NT',
                ],
            }
        },

        created() {
            this.editable = Api.checkAuthUser();

            if (this.editable) {
                let vm = this;
                
                Bus.$on('idealRoleDetails', function(details) {
                    if (details) {
                        vm.introduction = details.introduction;
                        vm.when = details.when;
                        vm.max_distance = details.max_distance && details.max_distance != '' ? details.max_distance : 0;
                        vm.willing_to_relocate = details.willing_to_relocate;
                        vm.state = details.state;
                        vm.countries = details.countries;
                        vm.selected = vm.state ? vm.state.split(',') : [];
                        vm.selected_countries = vm.countries ? vm.countries.split(',') : [];
                    }
                });

                Bus.$on('removeIdealRole', function() {
                    vm.submit('clear');
                });
            }
        },

        methods: {

            formatWhen(m) {
                return (m == 1) ? 'In 1 month' : 'In ' + m + ' months';
            },

            formatWhenMonth(m) {
                if (m && m != '') {
                    let d = new Date();
                    d.setMonth(d.getMonth() + parseInt(m));

                    return Utils.getMonth(d.getMonth()) + ' ' + d.getFullYear();
                }
            },

            close() {

            },
            
            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },

            deleteRecord() {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteIdealRole');
            },

            submit(action = 0) {
                $('#modalIdealRole').modal('hide');

                Bus.$emit('onboardingSubmitWorkPreferences', action);
            },
        },
        components: {
            MainModal,
            WorkPreferences,
            EditIcon,
        },
    }
</script>