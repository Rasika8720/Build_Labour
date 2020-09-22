<template>
    <div class="profile-item-2">
        <div class="profile-content">
            
            <employment-modal></employment-modal>
            
            <div @click="action(-1)">
                <edit-icon cls="add-icon" data-target="#modalEmployment" icon-name="plus"></edit-icon>
            </div>
            
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h24v24H0z"/>
                        <path fill="#00aeef" d="M23 13.713A6.752 6.752 0 0 0 10.616 16H3a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h4V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1h4a2 2 0 0 1 2 2v6.713zM10.505 17a6.729 6.729 0 0 0 1.95 5H3a2 2 0 0 1-2-2v-4c.456.607 1.182 1 2 1h2v1a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-1h1.505zM8.4 5h7.2v-.9a.7.7 0 0 0-.7-.7H9.1a.7.7 0 0 0-.7.7V5zM6 17h2v1H6v-1zm11.25 6a5.75 5.75 0 1 1 0-11.5 5.75 5.75 0 0 1 0 11.5zm2.653-2.796L17.8 17.336V13a.5.5 0 1 0-1 0v4.664l2.297 3.132a.5.5 0 1 0 .806-.592z"/>
                    </g>
                </svg>
                
                Employment History
            </div>
        
            <employment-list></employment-list>
        </div>
    </div>
</template>

<script>
    import EmploymentModal from '../profile/EmploymentModal';
    import EmploymentList from '../profile/EmploymentList';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "employment",
        data() {
            return {

            }
        },
        created() {

        },
        methods: {
            action(index) {
                Bus.$emit('showEmployment', index, null);
            },
        },
        components: {
            EmploymentModal,
            EmploymentList,
            EditIcon,
        },
    }
</script>