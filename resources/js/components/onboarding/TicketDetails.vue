<template>
    <form class="modal-form" method="POST" @submit.prevent="submit">
        <div class="me-label">
            I have a current valid White Card
        </div>
        <div class="bl-inline">
            <input id="has_whitecard_1" class="styled-checkbox-round" type="checkbox"
                ref="has_whitecard_1"
                @focus="hasFocus(false)"
                @change="formatCheckbox('has_whitecard', 1)" />
            <label for="has_whitecard_1">Yes</label>

            <input id="has_whitecard_0" class="styled-checkbox-round" type="checkbox"
                ref="has_whitecard_0"
                @focus="hasFocus(false)"
                @change="formatCheckbox('has_whitecard', 0)" />
            <label for="has_whitecard_0">No</label>
        </div>

        <div class="me-label" style="margin-bottom:-15px">
            I have current valid licences and/or tickets in
        </div>
        <div class="skill-label-3 mt-3">
            e.g. RIIMPO317E - Conduct Roller Operations
        </div>
        <div class="emp-row mt-3">
            <div class="ticket-col-left">
                <input class="form-control" type="text"  placeholder="Search" v-model="keyword"
                    @focus="hasFocus(true)"
                    @keyup="onSearch(keyword)" />
            </div>
            <div class="ticket-col-right">
                <button style="margin-left:0px;width:100%" class="add-button" type="button" @click="onAdd()">Add</button>
            </div>
            <span class="err-msg" v-if="errors.ticket">
                {{ errors.ticket }}
            </span>
        </div>

        <div class="emp-row" style="margin-top:0" v-if="has_focus && searchedTickets && searchedTickets.length > 0">
            <ul class="list-group">
                <li class="list-group-item" v-for="(ticket, idx) in searchedTickets" :key="idx"
                    @click="onSelect(ticket)">
                    {{ ticket.ticket }} {{ ticket.description ? ('- ' + ticket.description) : '' }}
                </li>
            </ul>
        </div>

        <div class="emp-row" v-for="(ticket, idx) in tickets" :key="idx">
            <span class="ticket-label">{{ ticket.ticket }} {{ ticket.description ? ('- ' + ticket.description) : '' }}</span>

            <span class="remove-ticket-icon" @click="onDelete(idx)">
                <img src="/img/icons/remove.png"
                    srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x">
            </span>
        </div>
    </form>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "ticket-details",
        data() {
            return {
                has_focus: false,
                keyword: '',
                has_whitecard: '',
                tickets: [],
                searchedTickets: [],
                selected: false,
                errors: {
                    ticket: ''
                },
                endpoints: {
                    save: '/api/v1/worker/tickets',
                },
                handleTimeout: null
            }
        },

        created() {
            let vm = this;

            Bus.$on('ticketsDetails', function(detailsArray, detail) {
                vm.tickets = detailsArray;

                vm.formatCheckbox('has_whitecard', detail);
            });

            Bus.$on('onboardingSubmitTickets', function() {
                let saveInput = {
                    tickets: vm.tickets.map(function (ticket) {
                                if (ticket.id) {
                                    return { ticket_id: ticket.id };
                                }

                                return { ticket: ticket.ticket, description: ticket.description };
                             }),
                    has_whitecard: vm.has_whitecard
                };

                Api.submitPromise(vm.endpoints.save, saveInput).then((result) => {

                    Bus.$emit('ticketsSubmitSuccess');
                    vm.errors.ticket = '';
                    $('#modalTickets').modal('hide');

                }).catch (error => {

                    vm.errors.ticket = error.response.data.message;
                    Bus.$emit('ticketsSubmitError');

                });

                Bus.$emit('ticketsDetails', vm.tickets, vm.has_whitecard);
            });

            Bus.$on('refreshTicketDetails', function() {

                Promise.resolve(Api.getWorkerTickets()).then(function(data) {

                    $('#modalTickets').modal();
                    vm.tickets = data.data.tickets;
                });
            });
        },

        methods: {
            hasFocus(has_focus) {
                this.has_focus = has_focus;
                console.log(this);
            },
            async onSearch(keyword) {

                let vm = this;

                vm.errors.ticket = '';

                if (this.handleTimeout) {

                    clearTimeout(this.handleTimeout);
                }

                if (keyword.length <= 0) {

                    vm.searchedTickets = [];

                    return 0;
                }

                this.handleTimeout = setTimeout(() => {

                    Api.getTicketsPromise(keyword).then((result) => {

                        vm.searchedTickets = result.data.tickets;
                    });

                }, 500);
            },
            onSelect(ticket) {
                this.selected = ticket;
                this.keyword = ticket.ticket + (ticket.description ? (' - ' + ticket.description) : '');
                this.searchedTickets = [];
            },
            onDelete(index) {
                this.tickets.splice(index, 1);
            },
            onAdd() { // TODO: Need to improve algo
                if (! this.selected) {
                    this.keyword = this.keyword.trim();

                    let description = this.keyword.substr(this.keyword.indexOf(' ') + 1);

                    this.selected = {
                        id: null,
                        ticket: this.keyword.split(' ')[0],
                        description: description ? description.replace(/^[^a-zA-Z]+/, '') : null,
                    }
                }

                if (! this.tickets.find(el => (this.selected.id && el.id === this.selected.id) ||
                                                (! this.selected.id && (el.ticket == this.selected.ticket)
                                                    && (el.description == this.selected.description)))) {

                    if (this.selected.ticket.length > 0) {

                        this.tickets.unshift(this.selected);
                        this.keyword = '';
                        this.errors.ticket = '';
                        this.has_focus = false;
                    }

                } else {

                    this.errors.ticket = 'Ticket already exists on selected list';
                }

                this.selected = false;
            },
            formatCheckbox(refName, value) {
                Utils.formatCheckbox(this.$refs, null, refName, value);

                this.has_whitecard = (value == 1 || value == 0) ? value : null;

                // if (value == 0) {
                //     this.tickets = [];
                // }
            },

        }
    }
</script>
