<template>
    <div class="profile-item-2">
        <div class="profile-content">

            <tickets-modal></tickets-modal>

            <div @click="open">
                <edit-icon cls="edit-icon" data-target=""></edit-icon>
            </div>
            
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 26 26">
                    <path fill="#00aeef" fill-rule="evenodd" d="M23.96 8.404l1.06 1.06a1 1 0 0 1 0 1.415L10.88 25.02a1 1 0 0 1-1.415 0l-1.06-1.06a1.5 1.5 0 0 0-2.122-2.122l-2.12-2.121a1.5 1.5 0 0 0-2.122-2.122l-1.06-1.06a1 1 0 0 1 0-1.415L15.12.98a1 1 0 0 1 1.415 0l1.06 1.06a1.5 1.5 0 0 0 2.122 2.122l2.12 2.121a1.5 1.5 0 0 0 2.122 2.122zM13.707 6.636a1 1 0 0 1 1.414 0l4.243 4.243a1 1 0 0 1 0 1.414l-7.071 7.071a1 1 0 0 1-1.414 0l-4.243-4.243a1 1 0 0 1 0-1.414l7.071-7.071zm1.414-1.414a1 1 0 0 0-1.414 0l-8.485 8.485a1 1 0 0 0 0 1.414l5.657 5.657a1 1 0 0 0 1.414 0l8.485-8.485a1 1 0 0 0 0-1.414l-5.657-5.657z"/>
                </svg>

                Tickets
            </div>

            <div class="bl-label-16-style-2 mb-3" v-if="has_whitecard">
                <div class="cross-check-mark" style="font-size:1.2rem" v-if="mark_icon == 'check'">&#10003;</div>
                <div class="cross-check-mark" style="font-size:1rem" v-if="mark_icon == 'cross'">&#10005;</div>
                {{ has_whitecard }}
            </div>
            
            <div class="row">
                <div class="col-md-6 col-sm-6" v-for="first in firstColumn" v-bind:key="first.id">
                    <span class="bl-label-15">
                        {{ first.ticket }}
                    </span>
                    <span class="bl-label-14">
                        {{ first.description }}
                    </span>
                </div>

                <div class="col-md-6 col-sm-6" v-for="second in secondColumn" v-bind:key="second.id">
                    <span class="bl-label-15">
                        {{ second.ticket }}
                    </span>
                    <span class="bl-label-14">
                        {{ second.description }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TicketsModal from '../profile/TicketsModal';
    import EditIcon from '../common/EditIcon';

    export default {
        name: "tickets",
        data() {
            return {
                mark_icon: '',
                has_whitecard: '',
                tickets: [],
                firstColumn: [],
                secondColumn: [],
            }
        },

        created() {
            let vm = this;

            Bus.$on('ticketsDetails', function(detailsArray, detail) {
                vm.tickets = detailsArray;

                if (detail == 1) {
                    vm.mark_icon = 'check';
                    vm.has_whitecard = 'Has a valid and current White Card';
                
                } else if (detail == 0) {
                    vm.mark_icon = 'cross';
                    vm.has_whitecard = 'No valid and current White Card';
                }
                
                vm.display();
            });
        },

        methods: {
         
            open() {
                Bus.$emit('refreshTicketDetails');
            },

            display() {
                let len = this.tickets.length;
                let half = Math.ceil(len / 2);

                this.firstColumn = this.tickets.slice(0, half);
                this.secondColumn = this.tickets.slice(half, len);
            },
        },
        components: {
            TicketsModal,
            EditIcon,
        },
    }
</script>