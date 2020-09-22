<template>
    <div class="profile-item-2">
        <div class="profile-content">

            <education-modal></education-modal>

            <div @click="action(-1)">
                <edit-icon cls="add-icon" data-target="#modalEducation" icon-name="plus"></edit-icon>
            </div>
            
            <span class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h24v24H0z"/>
                        <path fill="#00aeef" d="M0 9.5l12-6 12 6-12 6-12-6zm3.429 2.571L12 16.357l8.571-4.286v5.143c-1.714.286-3.428.857-5.142 1.715-1.715.857-2.858 1.714-3.429 2.571-.571-.857-1.714-1.714-3.429-2.571-1.714-.858-3.428-1.429-5.142-1.715v-5.143z"/>
                    </g>
                </svg>

                Education
            </span>
            
            <education-list></education-list>
        </div>
    </div>
</template>

<script>
    import EducationModal from '../profile/EducationModal';
    import EducationList from '../profile/EducationList';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "education",
        data() {
            return {
                
            }
        },

        created() {
            
        },

        methods: {

            action(index) {
                Bus.$emit('showEducation', index, null);
            },
        },
        components: {
            EducationModal,
            EducationList,
            EditIcon,
        },
    }
</script>