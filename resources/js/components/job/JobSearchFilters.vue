<template>
    <div class="profile-item-2">
        <div class="profile-content">
            <div class="header-label">Looking for</div>
            <div class="mt-3">
                <input
                        id="search_type_individuals"
                        class="styled-checkbox-round"
                        type="checkbox"
                        ref="search_type_individuals"
                        @change="formatCheckbox('search_type', 'individuals')"
                />
                <label for="search_type_individuals" v-show="userType != 'worker'">Individuals</label>

                <input
                        id="search_type_companies"
                        class="styled-checkbox-round"
                        type="checkbox"
                        ref="search_type_companies"
                        @change="formatCheckbox('search_type', 'companies')"
                />
                <label for="search_type_companies" v-show="userType != 'worker'">Companies</label>

                <input
                        id="search_type_jobs"
                        class="styled-checkbox-round"
                        type="checkbox"
                        ref="search_type_jobs"
                        @change="formatCheckbox('search_type', 'jobs')"
                />
                <label for="search_type_jobs">Jobs</label>
            </div>

            <div class="header-label mt-4" v-show="showTitle">Role Title</div>
            <div class="emp-row mt-2" v-show="showTitle">
                <input
                        type="text"
                        class="form-control search-input bg-search"
                        placeholder="Search"
                        v-model="input.title"
                        @keyup="onSearch('title')"
                />
            </div>

            <div class="header-label mt-4" v-show="showSearchKeyword">Job Title</div>
            <div class="emp-row mt-2" v-show="showSearchKeyword">
                <input
                        type="text"
                        class="form-control search-input bg-search"
                        placeholder="Search"
                        v-model="input.search_string"
                        @keyup="onSearchJobTitle(input.search_string)"
                />
            </div>

            <div class="header-label mt-4">Location</div>
            <div class="emp-row mt-2">
                <input
                        type="text"
                        class="form-control search-input bg-search"
                        placeholder="Search"
                        ref="loc-search"
                        v-model="address"
                        @keyup="onChangeLocation(address)"
                />
            </div>
            <div class="emp-row" style="margin-top:0" v-if="locations && locations.length > 0">
                <ul class="list-group">
                    <li
                            class="list-group-item"
                            v-for="(place, idx) in locations"
                            :key="idx"
                            @click="onSelectLocation(place.place_name)"
                    >{{ place.place_name }}</li>
                </ul>
            </div>
            <div class="mt-4"></div>
            <div v-for="(address, index) in addresses" :key="index">
                <input
                        :id="'address-styled-checkbox-'+index"
                        :ref="'address-'+index"
                        class="styled-checkbox"
                        type="checkbox"
                        :value="address"
                        @click="onSearch('address')"
                />

                <label :for="'address-styled-checkbox-'+index" class="bl-ellipsis">{{ address }}</label>
            </div>

            <div class="header-label mt-4" v-show="showIndustries">Industries</div>
            <div class="emp-row mt-2" v-show="showIndustries">
                <select class="form-control search-input" v-model="input.industry" v-on:change="onSearch('industry')">
                    <option value="">All</option>
                    <option v-for="(option,index) in company_business_types" v-bind:value="option.business_type" >{{ option.business_type }}</option>
                </select>
            </div>

            <div class="header-label mt-4" v-show="showEducation">Education</div>
            <div class="emp-row mt-2" v-show="showEducation">
                <input
                        type="text"
                        class="form-control search-input bg-search"
                        placeholder="Search"
                        v-model="input.education"
                        @keyup="onSearch('education')"
                />
            </div>

            <div class="header-label mt-4" v-show="showTickets">Tickets</div>
            <div class="emp-row mt-2" v-show="showTickets">
                <input
                        type="text"
                        class="form-control search-input bg-search"
                        placeholder="Search Ticket"
                        v-model="input.ticket"
                        @keyup="onSearchTicket(input.ticket)"
                        @keyup.delete="onSearchTicket(input.ticket)"
                />
            </div>
            <div class="emp-row" style="margin-top:0" v-if="searchedTickets.length > 0 ">
                <ul class="list-group">
                    <li class="list-group-item" v-for="(ticket, idx) in searchedTickets" :key="idx"
                        @click="onSelectTicket(ticket)">
                        {{ ticket.ticket }} - {{ ticket.description }}
                    </li>
                </ul>
            </div>
            <div class="header-label mt-4" v-show="showCompany">Company</div>
            <div class="emp-row mt-2" v-show="showCompany">
                <input
                        type="text"
                        class="form-control search-input bg-search"
                        placeholder="Search"
                        v-model="input.company"
                        @keyup="onSearch('company')"
                />
            </div>
            <div class="sort-wrapper" v-if="sort_options !== false">
                <hr>
                <div class="header-label mt-4">Sort By:</div>
                <div class="emp-row mt-2" >
                    <select class="form-control search-input " v-model="input.sort_option" @change="onSearch('sort')">
                        <option v-for="(option,index) in sort_options" v-bind:key="index" >{{ option }}</option>
                    </select>

                </div>
                <div class="emp-row mt-2" >
                    <select class="form-control search-input" v-model="input.sort_order" @change="onSearch('sort')">
                        <option value="asc">Ascending</option>
                        <option value="desc">Descending</option>
                    </select>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';

    export default {
        name: "job-search-filters",
        data () {
            return {
                showTitle: true,
                showSearchKeyword: false,
                showIndustries: true,
                showEducation: true,
                showTickets: true,
                showCompany: true,
                locations: [],
                company_business_types: [],
                address: '',
                addresses: [],
                searchTimeout: null,
                input: {
                    search_type: 'jobs',
                    search_string: '',
                    address: [],
                    education: '',
                    ticket: '',
                    industry: '',
                    company: '',
                    title: '',
                    sort_option: 'Role',
                    sort_order:'asc'
                },
                endpoints: {
                    open_search: '/api/v1/open-search',
                },
                available_sort_options:{
                    individuals:['Name','Role','Most Recent'],
                    companies:['Name','Industry/Trade','Most Recent'],
                    jobs:['Date']
                },
                sort_options:'',
                tickets: [],
                searchedTickets: [],
                userType: Api.getUserType()
            }
        },
        methods: {
            formatCheckbox (refName, value, flag = true) {
                this.$refs[refName + '_' + value].checked = true;

                if (value == 'individuals') {
                    this.$refs[refName + '_companies'].checked = false;
                    this.$refs[refName + '_jobs'].checked = false;
                    this.showTitle = true;
                    this.showIndustries = false;
                    this.showEducation = true;
                    this.showTickets = true;
                    this.showCompany = false;
                    this.sort_options = this.available_sort_options.individuals;
                    this.showSearchKeyword = false;

                } else if (value == 'companies') {
                    this.$refs[refName + '_individuals'].checked = false;
                    this.$refs[refName + '_jobs'].checked = false;
                    this.showTitle = false;
                    this.showIndustries = true;
                    this.showEducation = false;
                    this.showTickets = false;
                    this.showCompany = false;
                    this.sort_options = this.available_sort_options.companies;
                    this.input.sort_option = 'Name';
                    this.input.sort_option = 'Location';
                    this.showSearchKeyword = false;

                } else if (value == 'jobs') {
                    this.$refs[refName + '_individuals'].checked = false;
                    this.$refs[refName + '_companies'].checked = false;
                    this.showTitle = false;
                    this.showSearchKeyword = true;
                    this.showIndustries = true;
                    this.showEducation = true;
                    this.showTickets = true;
                    this.showCompany = true;
                    this.sort_options = this.available_sort_options.jobs;
                    this.input.sort_option = 'Date';
                    this.input.sort_order = 'desc';

                } else {
                    this.$refs[refName + '_individuals'].checked = false;
                    this.$refs[refName + '_companies'].checked = false;
                    this.$refs[refName + '_jobs'].checked = false;
                    this.sort_options = this.available_sort_options.jobs;
                }

                if (flag) {
                    window.history.pushState({ urlPath: '/job/search/all?type=' + value }, '', '/job/search/all?type=' + value);
                }

                this.input[refName] = value;
                this.onSearch(value);
            },
            async onChangeLocation (keyword) {

                let vm = this;

                if (vm.searchTimeout) {

                    clearTimeout(vm.searchTimeout)
                }

                vm.searchTimeout = setTimeout(() => {

                    if (keyword && keyword.length > 0) {

                        Promise.resolve(Api.getLocationsPromise(keyword)).then((data) => {

                            this.locations = (data.data && data.data.locations) ? data.data.locations.features : [];
                        });

                    } else {

                        this.locations = [];
                    }
                }, 300);
            },
            onSelectLocation (location) {

                if (this.addresses.indexOf(location.replace(', Australia', '')) < 0) {

                    this.addresses.push(location.replace(', Australia', ''));
                }

                this.address = '';
                this.locations = [];
                this.$refs['loc-search'].focus();
                this.$nextTick(() => {
                    this.$refs['loc-search'].focus();
                });
            },
            async onSearch (type) {
                let vm = this;

                console.log(this.input);
                this.input.address = [];
                for (let i = 0; i < this.addresses.length; i++) {
                    if (this.$refs['address-' + i][0].checked == true) {
                        this.input.address.push(this.addresses[i]);
                    }
                }

                if (vm.searchTimeout) {

                    clearTimeout(vm.searchTimeout)
                }

                vm.searchTimeout = setTimeout(() => {

                    axios.post(this.endpoints.open_search, this.input, Utils.getBearerAuth()).then(function (response) {
                        let data = response.data.data;

                        console.log(data);
                        Bus.$emit('openSearch' + data.search_type.charAt(0).toUpperCase() + data.search_type.substr(1), data.search_result);

                    }).catch(function (error) {

                        let inputErrors = Utils.handleError(error);
                        if (inputErrors) vm.errors = inputErrors;

                    }).finally((data) => {

                        vm.searchTimeout = null;
                    });
                }, 300);
            },
            getCompanyOptions() {

                let vm = this;

                Promise.resolve(Api.getCompanyOptions()).then(function(data) {

                    vm.company_business_types = data.business_types;
                });
            },
            onSearchTicket(keyword) {
                let vm = this;
                if (! keyword){
                    vm.searchedTickets = [];
                    vm.onSearch('ticket');
                    return false;
                }

                vm.searchedTickets = Api.getTickets(keyword);
            },
            onSelectTicket(ticket) {
                let vm = this;
                vm.searchedTickets = [];
                vm.input.ticket = ticket.ticket;
                vm.onSearch('ticket');
            },
            onSearchJobTitle(keyword)
            {
                Bus.$emit('openSearchKeyword', keyword);
            }
        },
        created () {
            let vm = this;

            Bus.$on('openSearchKeyword', function (keyword) {
                vm.input.search_string = keyword;
                vm.formatCheckbox('search_type', Utils.getUrlParams().type);
            });

            window.onpopstate = function (e) {
                vm.formatCheckbox('search_type', Utils.getUrlParams().type, false);
            };

            vm.getCompanyOptions();
        },
        mounted () {
            this.formatCheckbox('search_type', Utils.getUrlParams().type, false);
        },
    }
</script>

<style scoped>
    .header-label {
        font-size: 16px;
        font-weight: 600;
        color: #383d3f;
    }
    .search-input {
        padding: 12px;
        font-size: 16px;
        border-radius: 4px;
        border: solid 1px rgba(25, 25, 25, 0.32);
        background-color: #ffffff;
        height: auto;
    }
    .search-input.bg-search {
        padding-left: 40px;
        background-position: 9px 13px;
        background-repeat: no-repeat;
        background-image: url(/img/icons/search.png);
        background-image: -webkit-image-set(
                url(/img/icons/search@2x.png) 2x,
                url(/img/icons/search@3x.png) 3x
        );
    }
    .search-input::placeholder {
        color: #acbbbf;
    }
    .styled-checkbox-round + label {
        margin-right: 0px;
        margin-bottom: 18px;
        width: 100%;
    }
    .styled-checkbox-round:checked + label:before {
        background: #00aeef;
        border: 1px solid #00aeef;
    }
    .styled-checkbox-round:checked + label {
        color: #00aeef;
        font-weight: 600;
    }

    .styled-checkbox + label {
        margin-right: 0px;
        margin-bottom: 4px;
        width: 100%;
        font-size: 14px;
    }
    .styled-checkbox:checked + label:before {
        background: #00aeef;
        border: 1px solid #00aeef;
    }

    .styled-checkbox:checked + label:after {
        top: 11px;
        background: #fff;
        box-shadow: 2px 0 0 #fff, 4px 0 0 #fff, 4px -2px 0 #fff, 4px -4px 0 #fff,
        4px -6px 0 #fff, 4px -8px 0 #fff;
    }
</style>
