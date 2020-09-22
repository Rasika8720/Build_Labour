<template>
    <div class="profile-item-2">
        <div class="profile-content">
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h24v24H0z"></path>
                        <path fill="#00aeef" d="M17.818 8.226a2.175 2.175 0 0 1-2.182-2.168c0-1.197.977-2.167
                        2.182-2.167S20 4.86 20 6.058a2.175 2.175 0 0 1-2.182 2.168zM4 1h10.182v5.95A5.907 5.907
                        0 0 0 12 8.066a6.142 6.142 0 0 0-1.57 1.924L4 9.671V1zm6.926 9.707c.606-.846 1.206-1.481
                        1.802-1.907.893-.638 2.515-1.19 3.636 0 .747.794.02 2.89-2.182 6.284l1.797 2.412-1.08
                        5.322h-2.171v-4.706c-1.681-.76-2.707-1.392-3.08-1.896-.371-.505-.371-1.195
                        0-2.07h1.594l2.618-1.874a.378.378 0 0 0 .077-.541.415.415 0 0 0-.567-.082l-2.444
                        1.741c-1.45-.17-2.742-.329-3.876-.479-1.7-.224-1.758-2.848.692-2.557
                        1.633.195 2.694.313 3.184.353zm-1.62 6.277a4.7 4.7 0 0 0 .876.652c.304.174.91.469 ut1.818.886l-1.705 4.296H8.364l.942-5.834z"></path>
                    </g>
                </svg>
                Trades
            </div>
            <form>
                <div class="emp-row">
                    <label class="emp-form-label">Required Trade</label>
                    <select v-model="selectedTrade" @focus="onFocus('trades')">
                        <option v-for="trade in tradeList" :key="trade.id" :value="trade.id"> {{ trade.name}}</option>
                    </select>
                </div>

                <div class="job-title">Tickets</div>

                <div class="emp-row">
                    <div class="ticket-col-left">
                        <input class="form-control" type="text"  placeholder="Search" v-model="keyword" @focus="onFocus('tickets')" @keyup="onSearchTicket(keyword)" />
                    </div>
                    <div class="ticket-col-right">
                        <button style="margin-left: 0px; width: 100%;" class="add-button" type="button" @click="onAddTicket()">Add</button>
                    </div>
                    <span class="err-msg" v-if="errors.ticket">
                            {{ errors.ticket }}
                        </span>
                </div>

                <div class="emp-row" style="margin-top:0" v-if="searchedTickets.length > 0 && focusTo == 'tickets'">
                    <ul class="list-group">
                        <li class="list-group-item" v-for="(ticket, idx) in searchedTickets" :key="idx"
                            @click="onSelectTicket(ticket)">
                            {{ ticket.ticket }} - {{ ticket.description }}
                        </li>
                    </ul>
                </div>

                <div class="emp-row" v-for="(ticket, idx) in tickets" :key="'tkItem' + idx" v-if="tickets.length > 0">
                    <span class="ticket-label">{{ ticket.ticket }} - {{ ticket.description }}</span>

                    <span class="remove-ticket-icon" @click="onRemoveTicket(idx)">
                            <img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x">
                        </span>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: 'new-day-labour-trades',
        data() {
            return {
                tradeList: [],
                keyword: '',
                focusTo: null,
                selectedTrade: [],
                selectedSubTrade: [],
                searchedTickets: [],
                tickets: [],
                errors: []
            }
        },
        mounted() {
            this.searchTrades();
            Bus.$on('newDayLabourSave', this.onSave);
        },
        methods: {
            onFocus(type) {
                this.focusTo = type;
                Bus.$emit('clearDayLabourDetails');
                Bus.$emit('clearDayLabourRequirements');
            },
            searchTrades() {
                let vm = this;
                axios.get('/api/v1/trades/parents', Utils.getBearerAuth())
                    .then(function(response) {
                        vm.tradeList = response.data.data;
                    });
            },
            onSave() {
                let input = [this.selectedTrade, this.selectedSubTrade];

                Bus.$emit('newDayLabourTrades', input);
            },
            onSearchTicket(keyword) {
                if (! keyword) {
                    this.searchedTickets = '';
                    return;
                }

                this.searchedTickets = Api.getTickets(keyword);
            },
            onSelectTicket(ticket) {
                this.selected = ticket;
                this.keyword = ticket.ticket + ' - ' + ticket.description;
                this.searchedTickets = [];
            },
            onRemoveTicket(index) {

                this.tickets.splice(index, 1);
            },
            onAddTicket() {
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
        }
    }
</script>
