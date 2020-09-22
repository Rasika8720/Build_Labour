<template>
    <div>
        <div v-for="(education, idx) in educations" :key="idx" class="education-item">

            <div class="jobads-row mb-3">
                <div class="bl-col-2" style="margin-top:-4px">
                    <div class="bl-display">
                        <span class="bl-label-16 bl-ml15" :ref="'eduCourse-' + idx">
                            {{ education.course ? education.course.course_name : education.course_name }}
                        </span>
                        <span class="bl-label-15 bl-ml15 mt-0 pt-0" :ref="'eduSchool-' + idx">
                            {{ education.school_name }}
                        </span>
                        <span class="bl-label-14 bl-ml15 mb-0 pb-0" :ref="'eduPeriod-' + idx">
                            {{ formatPeriod(education) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import Avatar from '../common/Avatar';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "education-list",
        props: {
            educations: Array
        },
        data() {
            return {
                statuses: [
                    'Completed Study',
                    'Still Studying',
                ],
            }
        },
        created() {
            let vm = this;

            Bus.$on('educationDetails', function(detailsArray) {
                vm.educations = detailsArray;
            });

            Bus.$on('updateEducation', function(index, details) {
                if (index == -1) {
                    vm.educations.push(details);
                
                } else {
                    if (details.school_name !== vm.educations[index].school &&
                        window.location.pathname == '/user/profile') {
                        
                        Api.redirectToProfile();
                    }

                    vm.educations[index] = details;

                    vm.$refs['eduCourse-' + index][0].textContent = details.course ? details.course.course_name : details.course_name;
                    vm.$refs['eduSchool-' + index][0].textContent = details.school;
                    vm.$refs['eduPeriod-' + index][0].textContent = vm.formatPeriod(details);
                }
            });

            Bus.$on('removeEducation', function(index, id) {
                vm.educations.splice(index, 1);
            });
        },
        methods: {
            getInitials(name, school) {
                return Utils.getInitials((name != null) ? name.replace(' of ', ' ').replace('The ', '').replace(' the ', ' ') :
                        (school ? school.school_name.replace(' of ', ' ').replace('The ', '').replace(' the ', ' ') : '-'));
            },
            getColorHex(name) {
                if (! name) {
                    return '#F0F2F4';
                }
                
                return '#' + Utils.getColorHex('#' + name);
            },
            formatPeriod(edu) {
                if (edu.education_status == this.statuses[0]) { // Completed
                    return 'Date of Completion: ' + 
                            ('0' + edu.end_month).slice(-2) + '/' + ('0' + edu.end_day).slice(-2) + '/' + edu.end_year;

                } else if (edu.education_status == this.statuses[1]) { // Still Studying
                    return ('0' + edu.start_month).slice(-2) + '/' + ('0' + edu.start_day).slice(-2) + '/' + edu.start_year +
                            ' start — ' +
                            ('0' + edu.end_month).slice(-2) + '/' + ('0' + edu.end_day).slice(-2) + '/' + edu.end_year +
                            ' end';

                } else {
                    return 'N/A';
                }

                // if (! edu.end_month || ! edu.end_year) {
                //     return Utils.getMonth(edu.start_month - 1) + ' ' + edu.start_year + ' - Present (Currently Studying)';
                // }

                // return Utils.getMonth(edu.start_month - 1) + ' ' + edu.start_year + ' - ' + 
                //        Utils.getMonth(edu.end_month - 1) + ' ' + edu.end_year;
            },
            action(index) {
                Bus.$emit('showEducation', index, index != -1 ?  this.educations[index] : null);
            },
        },
        components: {
            Avatar,
            EditIcon,
        },
    }
</script>