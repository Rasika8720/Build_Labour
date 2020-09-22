<template>
    <main-modal id="modalTickets">

        <template slot="custom-modal-title">
            <h4 class="modal-title">Edit Tickets</h4>
            <div class="close" data-dismiss="modal" @click="close">&times;</div>
        </template>

        <template slot="custom-modal-content">
            <ticket-details></ticket-details>
        </template>

        <template slot="custom-modal-footer">
            <button class="pull-right" type="button" @click="submit">
                Save Changes
            </button>
        </template>

    </main-modal>
</template>

<script>
    import Api from '@/api';
    import MainModal from '../common/MainModal';
    import TicketDetails from '../onboarding/TicketDetails';

    export default {
        name: "tickets-modal",
        data() {
            return {

            }
        },
        created() {

        },
        methods: {
            close() {
                // Bus.$emit('refreshTicketDetails');
            },
            submit() {

                Bus.$emit('onboardingSubmitTickets');

                this.close();
            },
        },
        components: {
            MainModal,
            TicketDetails,
        },
    }
</script>