<template src="./RegisterCompany.htm"></template>

<script>
    import Api from '@/api';
    import MainModal from '../common/MainModal';
    import PhotoModal from '../common/PhotoModal';
    import PasswordEye from '../common/PasswordEye';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue';

    export default {
        name: "register-company",
        data() {
            return {
                has_focus_answer: false,
                has_focus_location: false,
                loading: false,
                width: 10,
                sections: null,
                step: 1,
                max: 1,
                orientation: '',
                subHeader: 'About the business',
                subHeaders: [
                    'About the business',
                    'About the business',
                    'Location & Contact',
                    'Login Credentials'
                ],
                specialtyLabel: '',
                specialtyLabels: [
                    'What is your trade?',
                    'What do you supply?',
                    'What type of design?',
                    'What type of education provider are you?'
                ],
                progressCls: [],
                showStates: false,
                showProgress: false,
                disabled: false,
                disabledNext: true,
                time_out: false,
                locations: [],
                business_types: [],
                location_timeout: false,
                tiers: [],
                main_company_functions: [],
                secondary_company_functions: [],
                main_function_answers: [],
                states: [
                    'QLD', 'NSW', 'SA', 'VIC', 'WA', 'ACT', 'TAS', 'NT',
                ],
                input: {
                    company_name: '', company_business_type_id: '', company_tier_id: '', company_main_function_answer: '',
                    company_address: '', company_contact_number: '', company_operate_outside_states: '', company_website: '',
                    company_states: [], company_main_company_id: '', company_secondary_functions: [], company_photo: '',
                    email: '', password: '', password_confirmation: '',
                },
                errors: {
                    company_name: '', company_main_company_id: '', company_secondary_functions: '', company_main_function_answer: '',
                    company_business_type_id: '', company_tier_id: '', company_photo: '',
                    company_address: '', company_contact_number: '', company_website: '', company_operate_outside_states: '', company_states: '',  
                    email: '', password: '', password_confirmation: '',
                },
                endpoints: {
                    login: '/login',
                    save: '/api/v1/auth/company/register',
                    company_options: '/api/v1/company/options',
                },
                phoneType: 'Mobile',
                phonePlaceholder: 'XXXX XXX XXX',
                phoneValue: 0,
                formattedPhoneValue: "0",
                preventNextIteration: false
            }
        },
        computed: {
            isFirstStep: function(){
                return (this.step === 1);
            },

            isLastStep: function(){
                return (this.step === this.max);
            },

            isFirstStep: function() {

                return (this.step <= 1);
            }
        },
        watch: {
            orientation: 'setCssVars',
        },
        created() {


            let vm = this;

            Bus.$on('regTogglePassword', function(type) {
                vm.$refs['regTogglePassword'].type = type;
            });

            Bus.$on('regToggleConfirm', function(type) {
                vm.$refs['regToggleConfirm'].type = type;
            });

            Bus.$on('croppedPhoto', function(photo_url) {
                vm.input.company_photo = photo_url;

                vm.showProgress = false;
                vm.width = 10;
            });

            Bus.$on('closePhotoModal', function() {
                $('#upload').val('');
            });

            // this.input.company_secondary_functions.push('');

            setTimeout(function() {
                vm.$sections = vm.$refs['compCardWrapper'].querySelectorAll('li');
                vm.max = vm.$sections.length;
                vm.goToStep(1);
            }, 1);

            this.initDragDrop();
            this.getCompanyOptions();
        },
        methods: {
            focusOut: function() {

                this.input.company_contact_number = this.input.company_contact_number.replace(/[^0-9]/g, '')

                this.input.company_contact_number = this.phoneType === 'Mobile' ? this.input.company_contact_number.replace(/(\d{2})(\d{4})(\d{4})/, '$1 $2 $3') : this.input.company_contact_number.replace(/(\d{4})(\d{3})(\d{3})/, '$1 $2 $3');

                this.input.company_contact_number = this.input.company_contact_number.substr(0, 11).replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '');

            },
            onSelectPhoneType(type) {

                this.phoneType = type;

                this.focusOut()
            },
            hasFocusAnswer(has_focus) {
                this.has_focus_answer = has_focus;
            },
            hasFocusLocation(has_focus) {
                this.has_focus_location = has_focus;
            },
            getCompanyOptions() {
                let vm = this;

                Promise.resolve(Api.getCompanyOptions()).then(function(data) {
                    vm.business_types = data.business_types;
                    vm.tiers = data.tiers;
                    vm.main_company_functions = data.main_company_functions;
                });
            },
            isNumber(event) {
                if (!/\d/.test(event.key) && event.key !== ' ') return event.preventDefault();
            },
            setNextDisabled(step) {

                switch (step) {
                    case 1:
                        if (this.input.company_name && this.input.company_main_company_id) {
                            // this.input.company_secondary_functions && this.input.company_secondary_functions[0] != '') {
                                
                            this.disabledNext = false;
                        } else {
                            this.disabledNext = true;
                        }

                        break;

                    case 2:
                        if (
                            this.input.company_business_type_id
                            && this.input.company_tier_id
                          //  && this.input.company_photo
                        ) {
                            this.disabledNext = false;
                        } else {
                            this.disabledNext = true;
                        }

                        break;

                    case 3:
                        if (this.input.company_address && this.input.company_contact_number && this.input.company_website &&
                            (this.input.company_operate_outside_states === 0 || this.input.company_operate_outside_states === 1)) {
                            
                            this.disabledNext = false;
                        } else {
                            this.disabledNext = true;
                        }

                        break;
                }
            },
            onKeyupCompanyName(e) {
                this.setNextDisabled(1);
            },
            onChangeMainCompanyFunctions(e) {
                this.secondary_company_functions = this.main_company_functions.find(el => el.id == e.target.value).items;

                this.setNextDisabled(1);

                this.input.company_main_function_answer = '';
                this.specialtyLabel = this.specialtyLabels[e.target.value - 2];
            },
            onChangeLocation(keyword) {

                if (keyword && keyword.length > 0) {

                    Promise.resolve(Api.getLocationsPromise(keyword)).then((data) => {

                        this.locations = (data.data && data.data.locations) ? data.data.locations.features : [];
                    });

                } else {

                    this.locations = [];
                }

            },
            onSelectLocation(location) {
                this.input.company_address = location;
                this.locations = [];

                this.setNextDisabled(3);
            },
            onSearchMainFunctionAnswer(keyword, main_id) {
                this.main_function_answers = (keyword && keyword.length > 0) ? Api.getMainFunctionAnswers(keyword, main_id) : [];
            },
            onSelectMainFunctionAnswer(answer) {
                this.input.company_main_function_answer = answer;

                this.main_function_answers = [];
            },
            // addNewEntity() {
            //     this.input.company_secondary_functions = this.input.company_secondary_functions.filter(r => r !== '');

            //     this.input.company_secondary_functions.push('');
            // },
            // removeEntity(index) {
            //     if (this.input.company_secondary_functions.length > 1) {
            //         this.input.company_secondary_functions.splice(index, 1);
            //     }
            // },
            formatOperate(index) {
                if (index == 1) {
                    this.$refs['rc-checkbox-1'].checked = true;
                    this.$refs['rc-checkbox-0'].checked = false;

                    this.input.company_operate_outside_states = 1;
                    this.showStates = true;

                } else if (index == 0) {
                    this.$refs['rc-checkbox-1'].checked = false;
                    this.$refs['rc-checkbox-0'].checked = true;

                    this.input.company_operate_outside_states = 0;
                    this.showStates = false;
                    
                    this.input.company_states = [];

                } else {
                    this.$refs['rc-checkbox-1'].checked = false;
                    this.$refs['rc-checkbox-0'].checked = false;

                    this.input.company_operate_outside_states = null;
                }

                this.setNextDisabled(3);
            },
            onClickProfilePhoto() {
                upload.click();
            },
            onFileChange(e) {
                Utils.onFileChange(e, 0, 'CompanyRegister');
            },
            onClickUpload() {
                let vm = this;

                this.showProgress = true;

                if (this.input.company_photo && this.width < 100) {
                    var elem = document.getElementById('myBar');
                    var id = setInterval(frame, 20);
                    
                    function frame() {
                        if (vm.width >= 100) {
                            clearInterval(id);
                        } else {
                            vm.width++;
                            elem.style.width = vm.width + '%'; 
                            elem.innerHTML = (vm.width != 100) ? vm.width * 1 + '%' : vm.width * 1 + '% Complete';
                        }
                        if (vm.width == 100) {
                            vm.setNextDisabled(2);
                        }
                    }
                }
            },
            async submit() {
                let vm = this;

                Utils.setObjectValues(this.errors, '');

                this.loading = true;
                this.disabled = true;
                
                await axios.post(vm.endpoints.save, vm.$data.input, Utils.getBearerAuth())
                    
                .then(function(response) {
                    let data = response.data.data;
                    
                    Api.setToken(data.token);

                    if (data.company.photo_url) {
                        Api.setNavAvatar('', data.company.photo_url);
                        
                    } else {
                        Api.setNavAvatar(Utils.getInitials(data.company.name), '');
                    }

                    Api.redirectToProfile();
                
                }).catch(function(error) {
                    if (error.response) {
                        let data = error.response.data;

                        if (data.errors.company_name || 
                            data.errors.company_main_company_id || 
                            data.errors.company_main_function_answer) {

                            vm.goToStep(1);
                            //vm.skip(-3);
                        }

                        else if (data.errors.company_business_type_id ||
                            data.errors.company_tier_id ||
                            data.errors.company_photo) {

                            vm.skip(-2);
                        }

                        else if (data.errors.company_address ||
                            data.errors.company_contact_number ||
                            data.errors.company_website ||
                            data.errors.company_operate_outside_states ||
                            data.errors.company_states) {

                            vm.skip(-1);
                        }

                        for (let key in data.errors) {
                            vm.errors[key] = data.errors[key] ? data.errors[key][0] : '';
                        }
                    }

                    Utils.handleError(error);
                });
                
                this.loading = false;
                this.disabled = false;
            },
            setCssVars() {
                this.$refs['compCardWrapper'].style.setProperty('--x', (((this.step * 100) - 100) * -1) + '%')
                this.$refs['compCardWrapper'].style.setProperty('--y', (((this.step * 100) - 100) * 0) + '%')
                this.$refs['compCardWrapper'].style.setProperty('--axis', 'row')
                this.$refs['compCardWrapper'].style.setProperty('--axis-reverse', 'row-reverse')
                this.$refs['compCardWrapper'].style.setProperty('--cross', 'column')
                this.$refs['compCardWrapper'].style.setProperty('--cross-reverse', 'column-reverse')
            },
            skip(step) {

                if (step < 0 && this.step > 0) {

                    this.step--;

                } else {

                    this.step += step;
                }

                this.disabledNext = true;
                //
                // if (this.input.company_main_company_id == 5) {
                //     this.step += step;
                // }
                //
                this.goToStep(this.step);
            },
            goToStep(step) {
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

            initDragDrop() {

                setTimeout(() => {

                    var $file = document.querySelector('input[type=file]');
                    var $dropper = document.querySelector('.dropper');

                    $dropper.ondragover = function(e) {
                        e.dataTransfer.dropEffect = 'copy';
                        e.preventDefault();
                        this.classList.add('over');
                    };
                    $dropper.ondragleave = function(e) {
                        e.preventDefault();
                        this.classList.remove('over');
                    };
                    $dropper.ondrop = function(e) {
                        e.preventDefault();
                        this.classList.remove('over');

                        var files = e.dataTransfer.files;

                        $file.files = files;

                        if ($file.files[0] != files[0] || $file.files.length != files.length) {
                            throw Error("Dragging files into an input[type=file] is not allowed.");
                        } else {
                            Utils.onFileChange(e, 0, 'CompanyRegister');
                        }
                    };

                }, 500);
            }
        },
        components: {
            MainModal,
            PhotoModal,
            PasswordEye,
            PulseLoader,
        },
    }
</script>

<style scoped>
    li {
        width: 510px;
    }
    @media (max-width: 630px) {
        li {
            width: 70vw;
        }
    }
    @media (width: 320px) {
        li {
            width: 200px;
        }
    }
    @media (width: 360px) {
        li {
            width: 240px;
        }
    }
    @media (width: 375px) {
        li {
            width: 255px;
        }
    }
    @media (width: 411px) {
        li {
            width: 291px;
        }
    }
    @media (width: 414px) {
        li {
            width: 294px;
        }
    }
</style>