<template>
    <div class="row">
        <div class="col-md-4">
            <div class="profile-item-2">
                <div class="profile-content">
                    <input class="page-search" id="search-key" type="text" placeholder="Search"
                        v-model="keyword" @keyup="onSearchJobPosts" />
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="profile-item-2">
                <div class="profile-content">
                    <input class="page-search-loc" id="search-loc" type="text" placeholder="Location"
                        v-model="location" @keyup="onSearchJobPosts" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "job-search",
        data() {
            return {
                keyword: '',
                location: '',
            }
        },
        created() {
            Bus.$emit('activateTab', 'search-jobs');
        },
        methods: {
            onSearchJobPosts() {
                Bus.$emit('searchJobPosts', this.keyword, this.location);
            },
        },
    }
</script>

<style scoped>
    @media (max-width: 768px) {
        .col-md-4, .col-sm-4 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
</style>
