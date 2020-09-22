<template>
    <div class="col-md-6 onboarding-wrapper">
        <div class="profile-item-2">
            <div class="profile-content pb-4" style="padding: 20px 0px 0px 0px">
                <!-- ; max-height: 650px; overflow: scroll; -->
                <div class="form-sub-header">{{ subHeader }}</div>

                <div class="comp-progress">
                    <div class="form-progress-2 bl-mr10" :class="progressCls[0]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[1]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[2]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[3]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[4]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[5]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[6]"></div>
                    <div class="form-progress-2 bl-mr10" :class="progressCls[7]"></div>
                    <div class="form-progress-2" :class="progressCls[8]"></div>
                </div>

                <employment-modal></employment-modal>

                <education-modal></education-modal>
                <tier-modal></tier-modal>

                <ul class="comp-card-wrapper" ref="compCardWrapper">
                    <li class="comp-card-list"><current-role></current-role></li>
                    <li class="comp-card-list"><employment-history></employment-history></li>
                    <li class="comp-card-list"><education-history></education-history></li>
                    <li class="comp-card-list"><ticket-details></ticket-details></li>
                    <li class="comp-card-list"><skill-details></skill-details></li>
                    <li class="comp-card-list"><skill-achievements></skill-achievements></li>
                    <li class="comp-card-list"><work-preferences></work-preferences></li>
                    <li class="comp-card-list"><work-information></work-information></li>
                    <li class="comp-card-list"><personal-details></personal-details></li>
                </ul>

                <div class="modal-footer">
                    <div class="col-lg-4 col-md-12 btn btn-link btn-delete p-0" @click="save">
                        Save and Finish later
                    </div>

                    <div class="pull-right onboarding-step-wrapper col-lg-8 col-md-12 p-0">
                        <button class="pull-right" type="button" @click="prev()" v-if="step > 1">
                            Back
                        </button>
                        <button class="pull-right ml-2 btn-to-employment" type="button" @click="submit">
                            {{ nextButton }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import EmploymentModal from '../profile/EmploymentModal';
    import EducationModal from '../profile/EducationModal';
    import CurrentRole from '../onboarding/CurrentRole';
    import EmploymentHistory from '../onboarding/EmploymentHistory';
    import EducationHistory from '../onboarding/EducationHistory';
    import SkillDetails from '../onboarding/SkillDetails';
    import TicketDetails from '../onboarding/TicketDetails';
    import SkillAchievements from '../onboarding/SkillAchievements';
    import WorkInformation from '../onboarding/WorkInformation';
    import WorkPreferences from '../onboarding/WorkPreferences';
    import PersonalDetails from '../onboarding/PersonalDetails';
    import TierModal from '../onboarding/TierModal';

    export default {
        name: "onboarding",
        data() {
            return {
                sections: null,
                step: 1,
                max: 1,
                orientation: '',
                workerDetail: null,
                subHeader: 'Employment History',
                progressCls: [],
                currentSection: '',
                subHeaders: [
                    'Starting your profile',
                    'Employment History',
                    'Education',
                    'Tickets',
                    'Main Industry Skills',
                    'Main Industry Achievements',
                    'Work Preferences',
                    'Work Information',
                    'Personal Details'
                ],
                nextButtons: [
                    'To Employment History',
                    'To Education',
                    'To Tickets',
                    'To Industry Skills',
                    'To Achievements',
                    'To Work Preferences',
                    'To Work Information',
                    'To Personal Details',
                    'Complete Profile'
                ],
                submitForms: [
                    'CurrentRole',
                    'EmploymentHistory',
                    'Education',
                    'Tickets',
                    'IndustrySkills',
                    'Achievements',
                    'WorkPreferences',
                    'WorkInformation',
                    'PersonalDetails',
                ],
                urlParams: Utils.getUrlParams()
            }
        },
        props: {
            mostRecentRole: {
                type: String,
                required: false,
            },
        },
        computed: {
            isFirstStep() {
                return this.step === 1;
            },
            isLastStep() {
                return this.step === this.max;
            },
            nextButton() {
                return this.nextButtons[this.step - 1];
            },
        },
        watch: {
            orientation: 'setCssVars',
        },
        created() {
            let vm = this;

            setTimeout(function() {

                vm.$sections = vm.$refs['compCardWrapper'].querySelectorAll('li');
                vm.max = vm.$sections.length;

                let step = 1;

                if (vm.urlParams.step) {

                    switch(vm.urlParams.step) {

                        case 'start':
                            step = 1;
                            break;

                        case 'employment':
                            step = 2;
                            break;

                        case 'educations':
                            step = 3;
                            break;

                        case 'tickets':
                            step = 4;
                            break;

                        case 'skills':
                            step = 5;
                            break;

                        case 'achievements':
                            step = 6;
                            break;

                        case 'preferences':
                            step = 7;
                            break;

                        case 'information':
                            step = 8;
                            break;

                        case 'deatils':
                            step = 9;
                            break;
                    }
                }
                vm.goToStep(step);
            }, 100);

            Bus.$on('ticketsSubmitSuccess', function() {

                 vm.next();
            })
        },
        methods: {
            save() {
                this.submit();

                Api.redirectToProfile();
            },
            next() {
                Bus.$emit('alertHide');

                if (this.step == this.nextButtons.length) {
                    Api.redirectToProfile();
                }

                this.goToStep(this.step + 1);
            },
            prev() {

                Bus.$emit('alertHide');

                if (this.step > 0) {

                    this.step--;
                }


                this.goToStep(this.step);
            },
            submit() {
                let vm = this;

                Bus.$emit('onboardingSubmit' + this.submitForms[this.step - 1]);

                // Bus.$on('onboardingSubmitStatus', function(message) {
                //     console.log('submission error on current role');
                //     console.log(message);
                //     if (vm.submitForms[vm.step - 1] != 'Tickets' && message.success === true) {
                //
                //         vm.next();
                //     }
                // });

                if (vm.submitForms[vm.step - 1] != 'Tickets') {

                    vm.next();
                }

            },
            setCssVars() {
                this.$refs['compCardWrapper'].style.setProperty('--x', (((this.step * 100) - 100) * -1) + '%')
                this.$refs['compCardWrapper'].style.setProperty('--y', (((this.step * 100) - 100) * 0) + '%')
                this.$refs['compCardWrapper'].style.setProperty('--axis', 'row')
                this.$refs['compCardWrapper'].style.setProperty('--axis-reverse', 'row-reverse')
                this.$refs['compCardWrapper'].style.setProperty('--cross', 'column')
                this.$refs['compCardWrapper'].style.setProperty('--cross-reverse', 'column-reverse')
            },
            goToStep(step) {

                switch (step) {

                    case 1 :
                        window.history.replaceState(null, null, "?step=start");
                        break;

                    case 2 :
                        window.history.replaceState(null, null, "?step=employment");
                        break;

                    case 3 :
                        window.history.replaceState(null, null, "?step=educations");
                        break;

                    case 4 :
                        window.history.replaceState(null, null, "?step=tickets");
                        break;

                    case 5 :
                        window.history.replaceState(null, null, "?step=skills");
                        break;

                    case 6 :
                        window.history.replaceState(null, null, "?step=achievements");
                        break;

                    case 7 :
                        window.history.replaceState(null, null, "?step=preferences");
                        break;

                    case 8 :
                        window.history.replaceState(null, null, "?step=information");
                        break;

                    case 9 :
                        window.history.replaceState(null, null, "?step=deatils");
                        break;
                }

                this.step = step > this.max ? this.max : step < 1 ? 1 : step;
                this.currentSection = this.$sections[this.step-1];

                this.$sections.forEach(function(section) {
                    section.classList.remove('current');
                });

                this.currentSection.classList.add('current');
                this.currentSection.scrollTop = 0;

                this.setCssVars();

                this.subHeader = this.subHeaders[step - 1];

                for (let i = 0; i < step - 1; i++) {
                    this.progressCls[i] = 'complete'
                }

                this.progressCls[step - 1] = 'active';

                for (let i = step; i < this.progressCls.length; i++) {
                    this.progressCls[i] = 'incomplete'
                }
            },
        },
        components: {
            EmploymentModal,
            EducationModal,
            CurrentRole,
            EmploymentHistory,
            EducationHistory,
            SkillDetails,
            TicketDetails,
            SkillAchievements,
            WorkInformation,
            WorkPreferences,
            PersonalDetails,
            TierModal,
        },
    }
</script>

<style scoped>
    li {
        width: 565px;
    }
    button {
        width: 200px;
    }
    @media (max-width: 600px) {
        li {
            width: 90vw;
        }
    }
    @media (max-width: 460px) {
        .modal-footer {
            padding-left: 16px;
            text-align: center;
        }
        button {
            width: 100%;
            margin-bottom: 20px;
        }
    }
    /*@media (width: 320px) {
        li {
            width: 280px;
        }
    }
    @media (width: 360px) {
        li {
            width: 320px;
        }
    }
    @media (width: 375px) {
        li {
            width: 335px;
        }
    }
    @media (width: 411px) {
        li {
            width: 371px;
        }
    }
    @media (width: 414px) {
        li {
            width: 374px;
        }
    }*/
    @media (min-width: 768px) {
        .col-md-6, .col-sm-6 {
            flex: 0 0 74%;
            max-width: 74%;
        }
    }
    @media (min-width: 960px), (max-width: 1024) {
        .col-md-6, .col-sm-6 {
            flex: 0 0 59%;
            max-width: 59%;
        }
    }
</style>
