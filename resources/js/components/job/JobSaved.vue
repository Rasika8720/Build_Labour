<template>
    <div class="profile-item-3">
        <div class="profile-content">
            <span class="bl-label-16">Saved</span>
            <ul class="list-items" v-if="bookmarks && bookmarks.length > 0">
                <li v-for="(bookmark, index) in bookmarks" :key="index">
                    <div class="jobads-row">
                        <div class="bl-col-1">
                            <img v-if="bookmark.company_photo" class="bl-image-32" :src="bookmark.company_photo"
                                @click="onClickCompanyPhoto(bookmark.company_id)">

                            <avatar v-else cls="bl-image-32" size="32" border="0" border-radius="8px"
                                :initials="getInitials(bookmark.company_name)"
                                :company-id="bookmark.company_id + ''">
                            </avatar>
                        </div>
                        <div class="bl-col-2" style="margin-top:-2px;cursor:pointer"
                            @click="onClickJobPost(bookmark.company_id, bookmark.job_id)">

                            <div class="bl-display">
                                <span class="bl-label-15 mt-0 pt-0">{{ bookmark.job_role }}</span>
                                <span class="job-text">
                                    {{ bookmark.company_name }} • {{ bookmark.location }}
                                </span>
                            </div>
                            <div class="bl-box-3"></div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    import Api from '@/api';
    import Avatar from '../common/Avatar';

    export default {
        name: "job-saved",
        data() {
            return {
                bookmarks: [],
            }
        },
        created() {
            let vm = this;

            Bus.$on('saveJobPost', function(bookmark, flag) {
                let bookmarks = vm.bookmarks;

                if (flag) {
                    bookmarks.push({
                        job_id: bookmark.id,
                        company_id: bookmark.company_id,
                        company_name: bookmark.company.name,
                        company_photo: bookmark.company.photo_url,
                        location: bookmark.location,
                        job_role: bookmark.title ? bookmark.title : bookmark.job_role_name,
                    });
                } else {
                    for (let i = 0; i < bookmarks.length; i++) {
                        if (bookmarks[i].job_id === bookmark.id) {
                            bookmarks.splice(i, 1);
                            break;
                        }
                    }
                }
            });

            this.getBookmarks();
        },
        methods: {
            getInitials(name) {
                return Utils.getInitials(name);
            },
            getBookmarks() {
                let vm = this;

                Promise.resolve(Api.getBookmarks()).then(function(data) {
                    vm.bookmarks = data.data.bookmarks;
                });
            },
            onClickCompanyPhoto(company_id) {
                Api.redirectToCompanyProfile(company_id);
            },
            onClickJobPost(company_id, job_id) {
                Api.redirectToJobPost(company_id, job_id);
            },
        },
        components: {
            Avatar,
        },
    }
</script>