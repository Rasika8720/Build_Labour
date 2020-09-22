<template>
    <div class="profile-item-2">
        <div class="profile-content">

            <delete-modal></delete-modal>

            <main-modal id="modalIndustrySkill">

                <template slot="custom-modal-title">
                    <h4 class="modal-title">Edit Industry Skills & Achievements</h4>
                    <div class="close" data-dismiss="modal" @click="close">&times;</div>
                </template>

                <template slot="custom-modal-content">
                    <form class="modal-form" method="POST" @submit.prevent="submit">
                        <div class="skill-label">
                            What are your main industry work achievements?
                        </div>

                        <textarea rows="3" ref="skillsIntro" class="form-control" style="overflow:hidden"
                            placeholder="Example: Worked on Rail link, saved $30,000 on budget, and delivered 2 weeks before project deadline."
                            @focus="textAreaAdjust('skillsIntro')"
                            @keyup="textAreaAdjust('skillsIntro')"
                            v-model="input.main_skill">
                        </textarea>

                        <span class="err-msg" v-if="errors.main_skill">{{ errors.main_skill }}</span>

                        <div class="skill-label">
                            What are your main industry skills?
                        </div>
                        <div class="skill-label-3">
                            e.g. Time Management, Communication Skills
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
                                <!-- <span class="delete-icon close-icon" @click="removeSkill(index)">X</span> -->
                                <span class="remove-skill-icon" @click="removeSkill(index)">
                                    <img src="/img/icons/remove.png"
                                        srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x">
                                </span>
                            </div>
                        </div>

                        <center>
                            <div class="btn btn-link btn-delete" @click="addNewSkill">
                                Add Skill
                            </div>
                        </center>

                    </form>
                </template>

                <template slot="custom-modal-footer">
                    <div class="btn btn-link btn-delete" data-dismiss="modal" @click="deleteRecord"
                        v-if="main_skill || user_skills.length != 0">
                        Delete
                    </div>

                    <button class="pull-right" type="submit" @click="submit" :disabled="disabled">
                        Save Changes
                    </button>
                </template>

            </main-modal>

            <edit-icon cls="edit-icon" data-target="#modalIndustrySkill"></edit-icon>

            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path fill="#FFF" d="M0 0h24v24H0z"/>
                        <path fill="#00aeef" d="M17 16.484v6.27l-5-1.5-5 1.5v-6.27A8.958 8.958 0 0 0 12 18c1.85 0 3.57-.558 5-1.516zM12 17a8 8 0 1 1 0-16 8 8 0 0 1 0 16zm0-5l3.527 1.854-.674-3.927 2.853-2.781-3.943-.573L12 3l-1.763 3.573-3.943.573 2.853 2.781-.674 3.927L12 12z"/>
                    </g>
                </svg>

                Main Industry Skills & Achievements
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 profile-intro" v-if="main_skill">
                    {{ main_skill }}
                </div>
                <div class="col-md-6 col-sm-6" v-for="first in firstColumn" v-bind:key="first.sid">
                    <span class="bl-label-15">
                        {{ first.sname }}
                    </span>
                    <span class="bl-label-14">
                        {{ first.lname }}
                    </span>
                </div>
                <div class="col-md-6 col-sm-6" v-for="second in secondColumn" v-bind:key="second.sid">
                    <span class="bl-label-15">
                        {{ second.sname }}
                    </span>
                    <span class="bl-label-14">
                        {{ second.lname }}
                    </span>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import MainModal from '../common/MainModal';
    import DeleteModal from '../common/DeleteModal';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "industry-skills",
        data() {
            return {
                disabled: false,
                is_empty: false,
                main_skill: '',
                user_skills: [],
                firstColumn: [],
                secondColumn: [],
                levels: [
                    { id: 1, name: 'Beginner' },
                    { id: 2, name: 'Competent' },
                    { id: 3, name: 'Expert' },
                ],
                input: {
                    main_skill: '', skills: [],
                },
                errors: {
                    main_skill: ''
                },
                endpoints: {
                    save: '/api/v1/user/skills',
                    delete: '/api/v1/user/skills',
                },
            }
        },

        created() {
            let vm = this;

            Bus.$on('industrySkillsDetails', function(detailsArray, skillsIntro) {
                vm.main_skill = skillsIntro;
                vm.user_skills = detailsArray.map(function(skill) {
                    return {
                        sid: skill.skill_id,
                        sname: skill.skill_name,
                        lid: skill.level_id,
                        lname: skill.level_name
                    };
                });

                if (detailsArray.length == 0) {
                    vm.is_empty = true;

                } else {
                    vm.input.skills = detailsArray;
                }

                vm.input.main_skill = vm.main_skill;

                vm.display();
            });

            Bus.$on('removeIndustrySkill', function() {
                vm.main_skill = '';
                vm.user_skills = [];
                vm.display();
            });
        },

        methods: {

            display() {
                let len = this.user_skills.length;
                let half = Math.ceil(len / 2);

                this.firstColumn = this.user_skills.slice(0, half);
                this.secondColumn = this.user_skills.slice(half, len);
            },

            textAreaAdjust(refName) {
                Utils.textAreaAdjust(this.$refs[refName]);
            },

            close() {
                this.input.main_skill = this.main_skill;
                this.input.skills = this.user_skills.map(function(skill) {
                    return {
                        skill_id: skill.sid,
                        skill_name: skill.sname,
                        level_id: skill.lid,
                        level_name: skill.lname
                    };
                });

                Utils.setObjectValues(this.errors, '');
            },

            deleteRecord() {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteIndustrySkill', this.endpoints.delete);
            },

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

            async submit() {
                let vm = this;

				Utils.setObjectValues(vm.errors, '');

                vm.disabled = true;

                await axios.post(vm.endpoints.save, vm.$data.input, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    $('#modalIndustrySkill').modal('hide');

                    vm.is_empty = false;

                    vm.main_skill = data.data.main_skill;
                    vm.user_skills = data.data.skills.map(function(skill) {
                        return {
                            sid: skill.skill_id,
                            sname: skill.skill_name,
                            lid: skill.level_id,
                            lname: skill.level_name
                        };
                    });

                    vm.display();

                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);

                    if (inputErrors) vm.errors = inputErrors;
                });

                vm.disabled = false;
            },

        },
        components: {
            MainModal,
            DeleteModal,
            EditIcon,
        },
    }
</script>
