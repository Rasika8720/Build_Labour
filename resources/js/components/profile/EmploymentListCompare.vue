<template>
    <ul class="list-main-items mb-0" v-if="employments.length > 0">
        <li class="main-items mb-3"
            v-for="(employment, index) in employments"
            v-bind:key="index">

            <span class="text-icon-2">
                <img :src="imgSrc" :srcset="imgSrcSet" :ref="'toggleImg-' + index" @click="toggle(index)">
            </span>
            <div class="jobads-row" @click="toggle(index)">
                <div class="bl-col-2">
                    <div class="bl-display">
                        <span class="bl-label-16 bl-ml15" :ref="'empJobRole-' + index">
                            {{ employment.job_role }}
                        </span>

                        <span class="bl-label-15 bl-ml15 mt-0 pt-0" :ref="'empCompanyName-' + index">
                            {{ getCompanyName(employment.company_name, employment.company) }}
                        </span>

                        <span class="bl-label-14 bl-ml15 mt-0 pt-0" :ref="'empPeriod-' + index">
                            {{ formatPeriod(employment) }}
                        </span>
                    </div>
                </div>
            </div>

            <div :class="getCls" :ref="'toggleCls-' + index">

                <div class="empinfo-row" :ref="'empLocationIcon-' + index">
                    <img style="margin-top:5px" class="text-icon bl-mt12" src="/img/icons/pinlocation.png"
                         srcset="/img/icons/pinlocation@2x.png 2x, /img/icons/pinlocation@3x.png 3x">

                    <span style="display:inline" class="bl-label-14-style-2" :ref="'empLocation-' + index">
                        {{ getLocation(employment.location, employment.company) }}
                    </span>
                </div>

                <div class="empinfo-row" :ref="'empProjectSizeIcon-' + index">
                    <img class="text-icon" src="/img/icons/dollarsign.png"
                         srcset="/img/icons/dollarsign@2x.png 2x, /img/icons/dollarsign@3x.png 3x">

                    <span style="display:inline" class="bl-label-14-style-2" :ref="'empProjectSize-' + index">
                        {{ employment.project_size }}
                    </span>
                </div>

                <div class="empinfo-row" v-if="employment.responsibilities.length != 0">

                    <img class="text-icon" src="/img/icons/responsibilities.png"
                         srcset="/img/icons/responsibilities@2x.png 2x, /img/icons/responsibilities@3x.png 3x">
                    <span style="display:inline" class="bl-label-14-style-2">
                        Responsibilities:
                    </span>
                </div>

                <div class="bl-label-15" v-if="employment.responsibilities.length != 0">
                    <ul class="list-items">
                        <div v-for="(res, idx) in employment.responsibilities" :key="idx">
                            <li :ref="'empRespItem-' + index + '-' + idx">{{ res }}</li>
                        </div>
                    </ul>
                </div>

            </div>

            <div :class="getBox" :ref="'boxCls-' + index"></div>
        </li>
    </ul>
</template>

<script>

    import Api from '@/api';
    import Avatar from '../common/Avatar';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "employment-list-compare",
        props: {
            employments: Array
        },
        data() {
            return {
                expanded: [],
                getBox: 'bl-box-2 hidden',
                getCls: 'responsibilities hidden',
                imgSrc: '/img/icons/expand.png',
                imgSrcSet: '/img/icons/expand@2x.png 2x, /img/icons/expand@3x.png 3x',
            }
        },
        created() {
            let vm = this;

            Bus.$on('employmentDetails', function(detailsArray) {
                vm.employments = detailsArray;
                
                for (let i = 0; i < vm.employments.length; i++) {
                    vm.expanded[i] = false;
                }
            });

            Bus.$on('updateEmployment', function(index, details) {
                if (details) {
                    let emps = vm.employments;
                    
                    if (emps[index] && ((emps.length > 1 && details.isCurrent == 1 &&
                        vm.formatPeriod(details) != vm.formatPeriod(emps[index])) || 
                        (details.responsibilities.length > emps[index].responsibilities.length))) {
                        
                        if (window.location.pathname == '/user/profile') { // for onboarding, need to revise
                            Api.redirectToProfile();
                        }
                    }

                    if (index == -1) {
                        emps.push(details);
                    
                    } else {
                        
                        emps[index] = details;
                        
                        let refLocation = vm.$refs['empLocation-' + index],
                            refProjectSize = vm.$refs['empProjectSize-' + index],
                            refLocationIcon = vm.$refs['empLocationIcon-' + index],
                            refProjectSizeIcon = vm.$refs['empProjectSizeIcon-' + index];

                        vm.$refs['empJobRole-' + index][0].textContent = details.job_role;
                        vm.$refs['empCompanyName-' + index][0].textContent = vm.getCompanyName(details.company_name, details.company);
                        vm.$refs['empPeriod-' + index][0].textContent = vm.formatPeriod(details);

                        let loc = vm.getLocation(details.location, details.company);

                        if (loc) {
                            refLocation[0].textContent = loc;
                            refLocationIcon[0].hidden = false;
                        
                        } else {
                            refLocation[0].textContent = '';
                            refLocationIcon[0].hidden = true;
                        }

                        if (details.project_size) {
                            refProjectSize[0].textContent = details.project_size;
                            refProjectSizeIcon[0].hidden = false;
                        
                        } else {
                            refProjectSize[0].textContent = '';
                            refProjectSizeIcon[0].hidden = true;
                        }

                        for (let i = details.responsibilities.length; i < details.responsibilities.length; i++) {
                            vm.$refs['empRespItem-' + index + '-' + i][0].textContent = details.responsibilities[i];
                        }
                    }
                }
            });

            Bus.$on('removeEmployment', function(index) {
                vm.employments.splice(index, 1);
            });
        },
        methods: {
            getInitials(name, company) {
                return Utils.getInitials((name != null) ? name : company.name);
            },
            getCompanyName(name, company) {
                return (name != null) ? name : company.name;
            },
            getLocation(location, company) {
                return (location != null) ? location : (company) ? company.address : '';
            },
            toggle(index) {
                this.expanded[index] === true ? this.collapse(index) : this.expand(index);
            },
            collapse(index) {
                this.$refs['boxCls-' + index][0].className = 'bl-box-2 hidden';
                this.$refs['toggleCls-' + index][0].className = 'responsibilities hidden';
                this.$refs['toggleImg-' + index][0].src = '/img/icons/expand.png';
                this.$refs['toggleImg-' + index][0].srcset = '/img/icons/expand@2x.png 2x, /img/icons/expand@3x.png 3x';

                this.expanded[index] = false;
            },
            expand(index) {
                let emp = this.employments[index],
                    empLoc = this.$refs['empLocationIcon-' + index][0];

                if (! emp.location && (! emp.company || (emp.company && ! emp.company.address))) {
                    empLoc.hidden = true;
                }

                if (! emp.project_size) {
                    empLoc.hidden = true;
                }
                
                this.$refs['boxCls-' + index][0].className = 'bl-box-2';
                this.$refs['toggleCls-' + index][0].className = 'responsibilities';
                this.$refs['toggleImg-' + index][0].src = '/img/icons/collapse.png';
                this.$refs['toggleImg-' + index][0].srcset = '/img/icons/collapse@2x.png 2x, /img/icons/collapse@3x.png 3x';

                this.expanded[index] = true;
            },
            collapseAll() {
                for (let index = 0; index < this.employments.length; index++) {
                    this.collapse(index);
                }
            },
            formatPeriod(emp) {
                let endDate = (emp.end_month && emp.end_year) ?
                              new Date(emp.end_year, emp.end_month-1, 1) : new Date();

                return Utils.formatPeriod(new Date(emp.start_year, emp.start_month-1, 1), endDate);
            },
            onClickCompanyPhoto(company_id) {
                Api.redirectToCompanyProfile(company_id);
            },
            action(index) {
                Bus.$emit('showEmployment', index, index != -1 ? this.employments[index] : null);
            },
        },
        components: {
            Avatar,
            EditIcon,
        },
    }
</script>