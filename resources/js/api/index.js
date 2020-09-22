import Axios from 'axios'
import VueCookie from 'vue-cookie'

class BuildLabourApi {

    constructor () {

        const prod = '';
        const local = '';

        this.tokenName = 'bl_token';
        this.initials = 'bl_initials';
        this.profile_photo_url = 'bl_profile_photo_url';
        this.user_type = 'bl_user_type';

        this.options = {
            Api: {
                Domain: local,
                Version: 'v1'
            }
        };

        this.time_out = false;
        this.searchResults = [];
        this.locations = [];
        this.companies = [];
        this.getResults = [];
        this.returnValue = [];

        this.endpoints = {
            job_roles: '/api/v1/roles/job/search',
            jobs: '/api/v1/job/search/filter',
            bookmarks: '/api/v1/bookmarks/posts/jobs',
            tickets: '/api/v1/tickets/search',
            locations: '/api/v1/locations',
            companies: '/api/v1/company/search',
            employees: '/api/v1/company/',
            company_options: '/api/v1/company/options',
            responsibilities: '/api/v1/job/',
            savedJobPosts: '/api/v1/bookmarks/posts/jobs/ids',
            countries: '/api/v1/countries',
            courses: '/api/v1/courses',
            worker_tickets: '/api/v1/worker/tickets',
            schools: '/api/v1/schools',
            main_function_answers: '/api/v1/answers/',
            userid: '/api/v1/userid',
            tradeParents: '/api/v1/trades/parents',
            tradeChildren: '/api/v1/trades/children'
        };

        //  this._headers()
    }

    _headers () {
        Axios.defaults.headers.put['Content-Type'] = 'application/json';
        Axios.defaults.headers.post['Content-Type'] = 'application/json';
        Axios.defaults.baseURL = this._apiURL();

        if(this.isAuthenticated())
            this._invalidateToken();
    }

    _apiURL () {
        return [ this.options.Api.Domain, this.options.Api.Version ].join('/')
    }

    _getBearerToken() {
        let token = JSON.parse(VueCookie.get(this.tokenName));
        return token
    }

    _invalidateToken() {
        Axios.defaults.headers.authorization = "Bearer " + this._getBearerToken()
    }

    isAuthenticated() {
        const authed = JSON.parse(VueCookie.get(this.tokenName));
        return authed != null;
    }

    setNavAvatar(initials, profile_photo_url) {
        VueCookie.set(this.initials, initials);
        VueCookie.set(this.profile_photo_url, profile_photo_url);
    }

    setUserType(user_type) {

        VueCookie.set(this.user_type, user_type);
    }

    getUserType() {
        return VueCookie.get(this.user_type);
    }

    getNavAvatar() {
        return { initials: VueCookie.get(this.initials),
                 profile_photo_url: VueCookie.get(this.profile_photo_url) };
    }

    setToken(token) {
        VueCookie.set(this.tokenName, JSON.stringify(token));
        this._invalidateToken()
    }

    deleteToken() {
        VueCookie.delete(this.tokenName);
        VueCookie.delete(this.initials);
        VueCookie.delete(this.profile_photo_url);
        window.location.href = '/login';
    }

    checkAuthUser() {

        if (! parseInt(window.location.pathname.split('/').pop(), 10)) {
            return true;
        }

        return false;
    }

    showingOwnProfile() {
        if( isNaN(window.location.pathname.split('/').pop())) {
            console.log('Firing NaN');
            return true;
        }
    }

    redirectToProfile() {
        if (window.location.pathname == '/user/profile') {
            window.location.reload();

        } else {
            window.location = '/user/profile';
        }
    }

    redirectToUserProfile(user_id) {
        window.location = '/user/profile/' + user_id;
    }

    redirectToCompanyProfile(company_id) {
        window.location = '/company/profile/' + company_id;
    }

    redirectToJobPost(company_id, job_id) {
        window.location = '/job/view/?cid=' + company_id + '&jid=' + job_id;
    }

    async _get(endpoint) {
        let vm = this;

        // if (vm.time_out) {
        //     clearTimeout(vm.time_out);
        // }

        // vm.time_out = await setTimeout(async function() {

            await Axios.get(endpoint, Utils.getBearerAuth())

            .then(function(response) {

                vm.getResults = response.data;

            }).catch(function(error) {

                Utils.handleError(error);
            });

        // }.bind(this), 100);

        return this.getResults;
    }

    async _post(endpoint, input) {

        let vm = this;

        await Axios.post(endpoint, input, Utils.getBearerAuth())

        .then(function(response) {

            vm.getResults = response.data;

        }).catch(function(error) {

            Utils.handleError(error);
        });

        return this.getResults;
    }

    async _search(endpoint) {
        let vm = this;

        // if (vm.time_out) {
        //     clearTimeout(vm.time_out);
        // }

        // vm.time_out = await setTimeout(async function() {

            await Axios.get(endpoint, Utils.getBearerAuth())

            .then(function(response) {

                vm.getResults = response.data;

            }).catch(function(error) {

                Utils.handleError(error);
            });

        // }.bind(this), 200);

        return this.getResults;
    }

    async submit(endpoint, input, emit = 'alertSuccess', emitInput = false, emitFail = 'alertFail') {

        await Axios.post(endpoint, input, Utils.getBearerAuth())

            .then(function(response) {
                console.log(response);
                console.log(response.data.message);

                if( emitInput === false)
                    Bus.$emit(emit, response.data);
                else
                    Bus.$emit(emit, input);

            }).catch(function(error) {
                console.log(error.response);
                // Bus.$emit('submitError', error.response.data.message);

                Bus.$emit(emit, error.response);
                Utils.handleError(error);
            });
    }

    submitPromise(endpoint, input) {

        return Axios.post(endpoint, input, Utils.getBearerAuth());
    }


    getTickets(keyword) {
        let self = this;

        Promise.resolve(self._search(this.endpoints.tickets + '?keyword=' + keyword)).then(function(data) {
            self.returnValue = data.data ? data.data.tickets : [];
        });

        return self.returnValue;
    }

    getTrades() {
        let self = this;

        Axios.get(this.endpoints.tradeParents, Utils.getBearerAuth())
            .then(function(response) {
                console.log(response);
                return response;
            });
    }

    getTicketsPromise(keyword) {

        let self = this;
        return self._search(this.endpoints.tickets + '?keyword=' + keyword);
    }

    getWorkerTickets() {
        return this._get(this.endpoints.worker_tickets);
    }

    getCompanyApplicants(endpoint) {
        return this._get(endpoint);
    }

    getLocations(keyword, types = '') {
        let self = this;

        Promise.resolve(self._search(this.endpoints.locations + '?keyword=' + keyword + '&types=' + types)).then(function(data) {
            self.returnValue = (data.data && data.data.locations) ? data.data.locations.features : [];
        });

        return self.returnValue;
    }

    getLocationsPromise(keyword, types = '') {
        let self = this;

        return self._search(this.endpoints.locations + '?keyword=' + keyword + '&types=' + types);
    }

    getCompanies(keyword) {
        let self = this;

        Promise.resolve(self._search(this.endpoints.companies + '?keyword=' + keyword)).then(function(data) {
            self.returnValue = data.data ? data.data.companies : [];
        });

        return self.returnValue;
    }

    getCompaniesPromise(keyword) {
        let self = this;

        return self._search(this.endpoints.companies + '?keyword=' + keyword);
    }

    getCompanyOptions() {
        return this._get(this.endpoints.company_options);
    }

    getJobResponsibilities(id) {
        return this._get(this.endpoints.responsibilities + id + '/responsibilities');
    }

    getCurrentUserId()
    {
        return this._get(this.endpoints.userid);
    }

    getCollectedJobResponsibilities(keyword, by) {

        return this._get(this.endpoints.responsibilities + 'collection/responsibilities?' + by + '=true&keyword=' + keyword);
    }

    getBookmarks() {
        return this._get(this.endpoints.bookmarks);
    }

    getSavedJobPosts() {
        return this._get(this.endpoints.savedJobPosts);
    }

    getJobPosts(endpoint) {
        return this._get(endpoint);
    }

    getJobs(role = '', tiers = [], sectors = [], locations = []) {

        let input = { role: role, tiers: tiers, sectors: sectors, locations: locations };

        return this._post(this.endpoints.jobs, input);
    }

    getJobRoles(keyword) {
        let self = this;

        Promise.resolve(self._search(this.endpoints.job_roles + '?keyword=' + keyword)).then(function(data) {
            self.returnValue = data.data ? data.data.job_roles : [];
        });

        return self.returnValue;
    }

    searchJobRoles(keyword) {
        let self = this;

        return Promise.resolve(self._search(this.endpoints.job_roles + '?keyword=' + keyword));
    }

    getCountries() {
        return this._get(this.endpoints.countries);
    }

    getCourses(keyword) {
        let self = this;

        Promise.resolve(self._search(this.endpoints.courses + '?keyword=' + keyword)).then(function(data) {
            self.returnValue = data.data ? data.data.courses : [];
        });

        return self.returnValue;
    }

    getEmployees(id) {
        return this._get(this.endpoints.employees + id + '/people');
    }

    getSchools(keyword) {
        let self = this;

        Promise.resolve(self._search(this.endpoints.schools + '?keyword=' + keyword)).then(function(data) {
            self.returnValue = data.data ? data.data.schools : [];
        });

        return self.returnValue;
    }

    getMainFunctionAnswers(keyword, main_id) {
        let self = this;

        Promise.resolve(self._search(this.endpoints.main_function_answers + main_id + '?keyword=' + keyword)).then(function(data) {
            self.returnValue = data.data ? data.data.answers : [];
        });

        return self.returnValue;
    }
}

export default new BuildLabourApi()
