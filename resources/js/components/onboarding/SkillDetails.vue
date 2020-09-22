<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">
        <div class="skill-label">
            What are your main industry skills?
        </div>
        <div class="skill-label-3">
            e.g. Time Management, Communication Skills
        </div>

        <div class="btn btn-link btn-delete" @click="addNewSkill">
            Add Skill
        </div>

        <div class="emp-row row-center"
            :ref="'skillItem-' + index"
            v-for="(skill, index) in input.skills"
            :key="index">

            <div class="emp-col-left">
                <input class="form-control" type="text" v-model="skill.skill_name" />
            </div>

            <div class="emp-col-mid">
                <select v-model="skill.level_id">
                    <option v-for="level in levels" :key="level.id" v-bind:value="level.id">{{ level.name }}</option>
                </select>
            </div>

            <div class="emp-col-right">
                <span class="remove-skill-icon" @click="removeSkill(index)">
                    <img src="/img/icons/remove.png"
                        srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x">
                </span>
            </div>
        </div>
    </form>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "skill-details",
        data() {
            return {
                is_empty: false,
                levels: [
                    { id: 1, name: 'Beginner' },
                    { id: 2, name: 'Competent' },
                    { id: 3, name: 'Expert' },
                ],
                input: {
                    skills: [],
                },
                endpoints: {
                    save: '/api/v1/user/skills',
                },
            }
        },
        created() {
            let vm = this;


            Bus.$on('onboardingDetails', (workerDetail) => {

                if (workerDetail) {

                    this.input.skills = workerDetail.skills ? workerDetail.skills : [];
                }
            });


            Bus.$on('onboardingSubmitIndustrySkills', function() {
                Api.submit(vm.endpoints.save, vm.$data.input, 'onboardingSubmitStatus');
            });
        },
        methods: {
            addNewSkill() {
                if (this.input.skills.length == 0 || this.input.skills.slice(-1)[0].skill_name !== '') {
                    this.input.skills.push({
                        skill_id: 0,
                        skill_name: '',
                        level_id: 1
                    });
                }
            },
            removeSkill(index) {
                this.input.skills.splice(index, 1);
            },
        },
    }
</script>
