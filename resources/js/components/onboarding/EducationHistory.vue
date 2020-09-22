<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">
        <div class="skill-label">
            List any formal qualifications you have started or <b>completed</b>
        </div>
        <div class="skill-label-3">
            e.g. Certificate II in Construction
        </div>

        <div class="btn btn-link btn-delete"
            data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target="#modalEducation"
            @click="action(-1)">

            Add Education
        </div>

        <education-list></education-list>

    </form>
</template>

<script>
    import Api from '@/api';
    import EducationList from '../profile/EducationList';

    export default {
        name: "education-history",
        data() {
            return {

            }
        },
        created() {
            let vm = this;

            Bus.$on('onboardingSubmitEducation', function() {
                Bus.$emit('alertSuccess', 'Successfully saved education');
            });
        },
        methods: {
            action(index) {
                Bus.$emit('showEducation', index, null);
            },
        },
        components: {
            EducationList,
        },
    }
</script>
