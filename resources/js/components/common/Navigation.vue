<template>
    <div class="row">

        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container">
                <a class="navbar-brand" href="javascript:void(0)"><img class="bl-nav-logo" src="/img/BUILDLABOUR_FULLLOGO@1x.png" width="90" @click="onClickLogo()"></a>

                <div class="nav-hamburger-wrapper">

                    <div class="bl-nav-tab-style-2" @click="showNotificationsMobile()" data-toggle="notification">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20">
                            <g fill="none" fill-rule="evenodd">
                                <path d="M-4-2h24v24H-4z"/>
                                <path fill="#FFF" fill-rule="nonzero" d="M8 20c1.1 0 2-.9 2-2H6c0 1.1.9 2 2 2zm6-6V9c0-3.07-1.63-5.64-4.5-6.32V2C9.5 1.17 8.83.5 8 .5S6.5 1.17 6.5 2v.68C3.64 3.36 2 5.92 2 9v5L.354 15.646A1.207 1.207 0 0 0 0 16.5a.5.5 0 0 0 .5.5h15a.5.5 0 0 0 .5-.5c0-.32-.127-.627-.354-.854L14 14zm-3 1H5a1 1 0 0 1-1-1V9c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v5a1 1 0 0 1-1 1z"/>
                            </g>
                        </svg>
                        <span class="unread-count unread-notifications" v-if="unread_notifications !== 0">
                                    {{unread_notifications}}
                                </span>
                    </div>
                    <ul ref="notificationsMobile" class="notification-menu notifications-mobile dropdown-menu dropdown-menu-left">
                        <li class="dropdown-item" v-if="unread_notifications == 0">
                            No New Notifications
                        </li>
                        <li class="dropdown-item"
                            v-for="notification in notifications"
                            :key="notification.id"
                            @click="readNotification(notification, notification.data.link)">
                            {{notification.data.message}}
                        </li>
                    </ul>

                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle navbar-toggler" data-toggle="dropdown">
                            <span class="icon-bar top-bar"></span>
                            <span class="icon-bar middle-bar"></span>
                            <span class="icon-bar bottom-bar"></span>
                            <span class="sr-only">Toggle navigation</span>
                        </a>
                        <div class="dropdown-menu">
                            <!-- menu dropdown mobile mode -->
                            <ul class="navbar-nav ml-auto bl-nav-dev-sm">
                                <li ref="nav-mob-profile" class="nav-item">
                                    <a class="nav-link" href="javascript:void(0)" ref="nav-profile" @click="onClickProfile">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                            <g fill="none" fill-rule="evenodd">
                                                <path d="M-2-2h24v24H-2z"/>
                                                <path fill="#FFF" fill-rule="nonzero" d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zM5.07 16.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78A7.893 7.893 0 0 1 10 18c-1.86 0-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33A7.95 7.95 0 0 1 2 10c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM10 4C8.06 4 6.5 5.56 6.5 7.5S8.06 11 10 11s3.5-1.56 3.5-3.5S11.94 4 10 4zm0 5c-.83 0-1.5-.67-1.5-1.5S9.17 6 10 6s1.5.67 1.5 1.5S10.83 9 10 9z"/>
                                            </g>
                                        </svg>
                                        <span>
                                    Profile
                                </span>
                                    </a>
                                </li>

                                <li ref="nav-mob-search-jobs"  class="nav-item">
                                    <a class="nav-link" href="javascript:void(0)" ref="nav-profile" @click="onClickNavSearch">
                                        <svg class="svg-icon" viewBox="0 0 20 20" width="20" height="20">
                                            <path fill="#FFF" fill-rule="nonzero"  d="M18.125,15.804l-4.038-4.037c0.675-1.079,1.012-2.308,1.01-3.534C15.089,4.62,12.199,1.75,8.584,1.75C4.815,1.75,1.982,4.726,2,8.286c0.021,3.577,2.908,6.549,6.578,6.549c1.241,0,2.417-0.347,3.44-0.985l4.032,4.026c0.167,0.166,0.43,0.166,0.596,0l1.479-1.478C18.292,16.234,18.292,15.968,18.125,15.804 M8.578,13.99c-3.198,0-5.716-2.593-5.733-5.71c-0.017-3.084,2.438-5.686,5.74-5.686c3.197,0,5.625,2.493,5.64,5.624C14.242,11.548,11.621,13.99,8.578,13.99 M16.349,16.981l-3.637-3.635c0.131-0.11,0.721-0.695,0.876-0.884l3.642,3.639L16.349,16.981z"></path>
                                        </svg>
                                        <span>Search</span>
                                    </a>
                                </li>

                                <li ref="nav-mob-jobs" class="nav-item">
                                    <a class="nav-link" href="javascript:void(0)" ref="nav-profile" @click="onClickJobs">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 18">
                                            <g fill="none" fill-rule="evenodd">
                                                <path d="M-1-3h24v24H-1z"/>
                                                <path fill="#FFF" fill-rule="nonzero" d="M20 0H2C.9 0 0 .9 0 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2zm-1 16H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1zM5 7h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm0-3h7a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2z"/>
                                            </g>
                                        </svg>
                                    <span>My Jobs</span>
                                    </a>
                                </li>
                                <li ref="nav-mob-messages" class="nav-item">
                                    <a class="nav-link" href="javascript:void(0)" ref="nav-messages" @click="onClickMessages">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                            <g fill="none" fill-rule="evenodd">
                                                <path d="M-2-2h24v24H-2z"/>
                                                <path fill="#FFF" fill-rule="nonzero" d="M18 3v10a1 1 0 0 1-1 1H3.584a1 1 0 0 0-.707.293L2 15.17V3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1zM2 0C.9 0 .01.9.01 2L0 20l4-4h14c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2H2zm3 10h6a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm0-3h10a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2zm0-3h9a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2z"/>
                                            </g>
                                        </svg>
                                        <span>
                                    Messages
                                <span class="unread-count" v-if="unread_messages !== 0">
                                    {{unread_messages}}
                                </span>
                                </span>
                                    </a>
                                </li>
                                <li ref="nav-mob-messages" class="nav-item">
                                    <a class="nav-link" href="javascript:void(0)" @click="onClickGoLogout()">
                                    <span>
                                        Logout
                                    </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="collapse navbar-collapse" id="navbarResponsive">

                    <!-- menu large devices mode -->
                    <ul class="row bl-nav-list bl-nav-dev-lg">
                        <li ref="nav-profile" @click="onClickProfile">
                            <div class="bl-nav-tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <g fill="none" fill-rule="evenodd">
                                        <path d="M-2-2h24v24H-2z"/>
                                        <path fill="#FFF" fill-rule="nonzero" d="M10 0C4.48 0 0 4.48 0 10s4.48 10 10 10 10-4.48 10-10S15.52 0 10 0zM5.07 16.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78A7.893 7.893 0 0 1 10 18c-1.86 0-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33A7.95 7.95 0 0 1 2 10c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM10 4C8.06 4 6.5 5.56 6.5 7.5S8.06 11 10 11s3.5-1.56 3.5-3.5S11.94 4 10 4zm0 5c-.83 0-1.5-.67-1.5-1.5S9.17 6 10 6s1.5.67 1.5 1.5S10.83 9 10 9z"/>
                                    </g>
                                </svg>
                            </div>
                            <p class="bl-nav-tab-label">Profile</p>
                        </li>

                        <li ref="nav-search-jobs" @click="onClickNavSearch">
                            <div class="bl-nav-tab">
                                <svg class="svg-icon" viewBox="0 0 20 20" width="20" height="20">
                                    <path fill="#FFF" fill-rule="nonzero"  d="M18.125,15.804l-4.038-4.037c0.675-1.079,1.012-2.308,1.01-3.534C15.089,4.62,12.199,1.75,8.584,1.75C4.815,1.75,1.982,4.726,2,8.286c0.021,3.577,2.908,6.549,6.578,6.549c1.241,0,2.417-0.347,3.44-0.985l4.032,4.026c0.167,0.166,0.43,0.166,0.596,0l1.479-1.478C18.292,16.234,18.292,15.968,18.125,15.804 M8.578,13.99c-3.198,0-5.716-2.593-5.733-5.71c-0.017-3.084,2.438-5.686,5.74-5.686c3.197,0,5.625,2.493,5.64,5.624C14.242,11.548,11.621,13.99,8.578,13.99 M16.349,16.981l-3.637-3.635c0.131-0.11,0.721-0.695,0.876-0.884l3.642,3.639L16.349,16.981z"></path>
                                </svg>
                            </div>
                            <p class="bl-nav-tab-label">Search</p>
                        </li>

                        <li ref="nav-jobs" @click="onClickJobs">
                            <div class="bl-nav-tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 18">
                                    <g fill="none" fill-rule="evenodd">
                                        <path d="M-1-3h24v24H-1z"/>
                                        <path fill="#FFF" fill-rule="nonzero" d="M20 0H2C.9 0 0 .9 0 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2zm-1 16H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1zM5 7h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm0-3h7a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2z"/>
                                    </g>
                                </svg>
                            </div>
                            <p class="bl-nav-tab-label">My Jobs</p>
                        </li>

                        <li ref="nav-messages" @click="onClickMessages">
                            <div class="bl-nav-tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <g fill="none" fill-rule="evenodd">
                                        <path d="M-2-2h24v24H-2z"/>
                                        <path fill="#FFF" fill-rule="nonzero" d="M18 3v10a1 1 0 0 1-1 1H3.584a1 1 0 0 0-.707.293L2 15.17V3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1zM2 0C.9 0 .01.9.01 2L0 20l4-4h14c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2H2zm3 10h6a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm0-3h10a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2zm0-3h9a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2z"/>
                                    </g>
                                </svg>
                            </div>
                            <p class="bl-nav-tab-label">Messages
                            <span class="unread-count" v-if="unread_messages !== 0">
                                {{unread_messages}}
                            </span>
                            </p>
                        </li>

                        <li ref="nav-jobAds" @click="onClickJobAds" v-if="urole==5">
                            <div class="bl-nav-tab">
                                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 18">
                                    <g fill="none" fill-rule="evenodd">
                                        <path d="M-1-3h24v24H-1z"/>
                                        <path fill="#FFF" fill-rule="nonzero" d="M20 0H2C.9 0 0 .9 0 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V2c0-1.1-.9-2-2-2zm-1 16H3a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1zM5 7h7a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2zm0-3h7a1 1 0 0 1 0 2H5a1 1 0 1 1 0-2z"/>
                                    </g>
                                </svg>
                            </div>
                            <p class="bl-nav-tab-label">Upload Job Ads</p>
                        </li>
                    </ul>

                    <div class="bl-nav-notification">
                        <logout text-only="false"></logout>

                        <div class="bl-nav-tab-style-2" @click="showNotifications()" data-toggle="notification">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20">
                                <g fill="none" fill-rule="evenodd">
                                    <path d="M-4-2h24v24H-4z"/>
                                    <path fill="#FFF" fill-rule="nonzero" d="M8 20c1.1 0 2-.9 2-2H6c0 1.1.9 2 2 2zm6-6V9c0-3.07-1.63-5.64-4.5-6.32V2C9.5 1.17 8.83.5 8 .5S6.5 1.17 6.5 2v.68C3.64 3.36 2 5.92 2 9v5L.354 15.646A1.207 1.207 0 0 0 0 16.5a.5.5 0 0 0 .5.5h15a.5.5 0 0 0 .5-.5c0-.32-.127-.627-.354-.854L14 14zm-3 1H5a1 1 0 0 1-1-1V9c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v5a1 1 0 0 1-1 1z"/>
                                </g>
                            </svg>
                            <span class="unread-count unread-notifications" v-if="unread_notifications !== 0">
                                    {{unread_notifications}}
                                </span>
                        </div>
                    </div>
                    <ul ref="notifications" class="notification-menu dropdown-menu dropdown-menu-left">
                        <li class="dropdown-item" v-if="unread_notifications == 0">
                            No New Notifications
                        </li>
                        <li class="dropdown-item"
                            v-for="notification in notifications"
                            :key="notification.id"
                            @click="readNotification(notification, notification.data.link)">
                            {{notification.data.message}}
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

    </div>
</template>

<script>

    import Api from '@/api';
    import Logout from '../auth/Logout';

    export default {
        name: "navigation",
        data() {
            return {
                urole:'',
                keyword: '',
                jobNavLabel: 'My Jobs',
                search_placeholder: 'Search Jobs, Individuals & Companies',
                unread_messages: 0,
                unread_notifications: 0,
                notifications: []
            }
        },
        created() {

            let vm = this;

            if (!Api._getBearerToken()) {

                Api.deleteToken()
            }

            Bus.$on('unreadCountReset', function() {
                vm.unread_messages = 0;
            });

            Bus.$on('activateTab', function(tabName) {
                console.log(tabName);
                vm.$refs['nav-' + tabName].style = 'opacity: 1';
                vm.$refs['nav-mob-' + tabName].style = 'opacity: 1';
            });

            if (Api.getUserType()) {

                switch(Api.getUserType()) {

                    case 'worker': this.jobNavLabel = 'Search Jobs'; break;
                    case 'company': this.jobNavLabel = 'My Jobs'; break;
                    default :
                        this.jobNavLabel = 'Search Jobs'
                        this.urole = '5'
                }
            }

            this.getUnreadMessage();
            this.getNotifications();

        },
        mounted() {
            if (window.location.pathname == '/job/search/all') {
                this.$refs['nav-search'].focus();
                this.$nextTick(() => {
                    this.$refs['nav-search'].focus();
                });
            }
        },
        methods: {
            getUnreadMessage()
            {
                let vm = this;
                axios.get('/api/v1/chat/unread', Utils.getBearerAuth())
                    .then(function(response) {
                        console.log(response);
                        vm.unread_messages = response.data.data;
                    });
            },

            onClickGoProfile() {

                Bus.$emit('triggerGoProfile');
            },

            onClickGoLogout() {

                Bus.$emit('triggerGoLogout');
            },

            onClickDashboard() {
                // this.$refs['nav-dashboard'].style = 'opacity: 1';
            },

            onClickProfile() {

                Api.redirectToProfile();
            },

            onClickLogo() {
                window.location.href = '/user/profile';
            },

            onClickJobAds() {
                window.location.href = '/user/UploadJobAds';
            },

            onClickJobs() {


                if (Api.getUserType() === 'company') {

                    window.location.href = '/job/list?type=active';

                } else {

                    window.location.href = '/job/applied';
                }
            },

            onClickMessages() {
                if (window.location.pathname != '/messages') {
                    window.location = '/messages';
                }
            },

            onClickNavSearch() {

                if (window.location.pathname != '/job/search/all') {

                    if (Api.getUserType() === 'company') {

                        window.location.href = '/job/search/all?type=individuals';

                    } else {

                        window.location.href = '/job/search/all?type=jobs';
                    }

                   // window.open('/job/search/all?type=individuals', '_blank');
                   // window.focus();
                }
            },
            onOpenSearch() {
                Bus.$emit('openSearchKeyword', this.keyword);
            },
            showNotifications()
            {
                jQuery(this.$refs.notifications).toggle();
            },
            showNotificationsMobile()
            {
                jQuery(this.$refs.notificationsMobile).toggle();
            },
            getNotifications()
            {
                let vm = this;

                axios.get('/api/v1/user/notifications', Utils.getBearerAuth())
                    .then(function(response){
                        vm.notifications = response.data.data;
                        vm.unread_notifications = vm.notifications.length;
                    });
            },
            readNotification(notification, link)
            {
                axios.post('/api/v1/notification/clear', {'notification': notification}, Utils.getBearerAuth())
                    .then(function(response) {
                        console.log(response);
                        window.location = link;
                    });
            }
        },
        components: {
            Logout,
        },
    }
</script>

<style>
    .unread-count {
        position: absolute;
        top: -4px;
        right: -12px;
        background: #f00;
        color: #fff;
        padding: 3px 9px;
        border-radius: 90px;
    }
    .unread-notifications {
        top: 7px;
        right: 0px;
        padding: 0px 7px;
    }

    .bl-nav-notification .unread-notifications {
        top: -7px;
        right: -20px;
    }

    .bl-nav-notification > div {
        position: relative;
    }

    .notification-menu {
        background: #fff;
        border: 1px solid #bababa;
        min-width: 300px;
        width: 100%;
        margin-left: 0;
        margin-top: 0;
    }

    .notification-menu .dropdown-item {
        white-space: normal;
    }

    .nav-hamburger-wrapper {
        width: 30%;
    }

    .nav-hamburger-wrapper > div {
        float: right;
    }

    .nav-hamburger-wrapper .bl-nav-tab-style-2 {
        float: right;
        margin-top: 21px;
        margin-left: 15px;
        opacity: 1;
    }

    .notifications-mobile {
        margin-top: 0;
    }
</style>
