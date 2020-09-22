<template>
    <transition name="slide-fade">
    <div class="profile-item-2" v-if="show">
        <ul class="list-job-items" v-for="(post, index) in posts" :key="index">
            <li class="job-items">
                <div class="profile-content">
                    <div class="jobads-row">
                        <div class="bl-col-1">
                            <img v-if="post.company.photo_url" class="bl-image-40" :src="post.company.photo_url">
                            <avatar v-else cls="bl-image-40" size="40" border="0" border-radius="8px"
                                :initials="getInitials(post.company.name)"
                                :company_id="post.company.id">
                            </avatar>
                        </div>
                        <div class="bl-col-2">
                            <div class="bl-display">
                                <span class="bl-label-19 bl-ml14">
                                    {{ post.company.name }}
                                </span>

                                <span class="bl-label-14 bl-ml14" style="margin-top:-5px">
                                    {{ getTimeDiffNow(post.created_at) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="bl-label-15 bl-mt16 bl-mb16">
                        {{ post.content }}
                    </div>
                    <div class="post-action" v-show="false">
                        <img class="mr-1" src="/img/icons/share.png"
                            srcset="/img/icons/share@2x.png 2x, /img/icons/share@3x.png 3x">

                        Share
                    </div>
                    <div class="post-action" v-show="false">
                        <img class="mr-1" src="/img/icons/comments.png"
                            srcset="/img/icons/comments@2x.png 2x, /img/icons/comments@3x.png 3x">

                        Comments
                    </div>
                    <div class="post-action" v-show="false">
                        <svg class="like" xmlns="http://www.w3.org/2000/svg" width="12" height="11" viewBox="0 0 12 11">
                            <g fill="none" fill-rule="evenodd">
                                <path d="M0 0h12v12H0z"/>
                                <path fill="#A2B2B7" fill-rule="nonzero" d="M.5 10.5h2v-6h-2v6zm11-5.5c0-.55-.45-1-1-1H7.345l.475-2.285.015-.16a.753.753 0 0 0-.22-.53L7.085.5l-3.29 3.295A.978.978 0 0 0 3.5 4.5v5c0 .55.45 1 1 1H9c.415 0 .77-.25.92-.61l1.51-3.525A.988.988 0 0 0 11.5 6V5z"/>
                            </g>
                        </svg>

                        Like
                    </div>
                </div>
            </li>
        </ul>
    </div>
    </transition>
</template>

<script>
    import Avatar from '../common/Avatar';

    export default {
        name: "company-posts",
        data() {
            return {
                show: true,
                posts: [],
                endpoints: {
                    get: '/api/v1/company/',
                },
            }
        },
        props: {
            companyId: {
                type: String,
                required: false
            },
        },
        computed: {
            endpointGet() {
                return this.endpoints.get + this.companyId + '/posts';
            }
        },
        created() {
            let vm = this;

            Bus.$on('showCompanyPosts', function(flag) {
                vm.show = flag;
                vm.getPosts();
                
                Bus.$emit('hideCompanyPeople');
                Bus.$emit('hideCompanyJobs');
            });

            Bus.$on('hideCompanyPosts', function() {
                vm.show = false;
            });

            this.getPosts();
        },
        methods: {
            getInitials(name) {
                return Utils.getInitials(name);
            },
            getPosts() {
                let vm = this;

                axios.get(vm.endpointGet, Utils.getBearerAuth())
                    
                .then(function(response) {
                    
                    vm.posts = response.data.data.posts;
                
                }).catch(function(error) {

                    Utils.handleError(error);
                });
            },
            getTimeDiffNow(created_at) {
                return Utils.formatTimeDiffNow(created_at);
            }
        },
        components: {
            Avatar,
        },
    }
</script>