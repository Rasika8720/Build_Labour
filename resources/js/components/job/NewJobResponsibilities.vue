<template>
<!--    <div class="profile-item-2">-->
<!--        <div class="profile-content">-->
<!--            <div class="profile-title">-->
<!--                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">-->
<!--                    <g fill="none" fill-rule="evenodd">-->
<!--                        <path d="M0 0h24v24H0z"/>-->
<!--                        <path fill="#00aeef" d="M17.818 8.226a2.175 2.175 0 0 1-2.182-2.168c0-1.197.977-2.167 2.182-2.167S20 4.86 20 6.058a2.175 2.175 0 0 1-2.182 2.168zM4 1h10.182v5.95A5.907 5.907 0 0 0 12 8.066a6.142 6.142 0 0 0-1.57 1.924L4 9.671V1zm6.926 9.707c.606-.846 1.206-1.481 1.802-1.907.893-.638 2.515-1.19 3.636 0 .747.794.02 2.89-2.182 6.284l1.797 2.412-1.08 5.322h-2.171v-4.706c-1.681-.76-2.707-1.392-3.08-1.896-.371-.505-.371-1.195 0-2.07h1.594l2.618-1.874a.378.378 0 0 0 .077-.541.415.415 0 0 0-.567-.082l-2.444 1.741c-1.45-.17-2.742-.329-3.876-.479-1.7-.224-1.758-2.848.692-2.557 1.633.195 2.694.313 3.184.353zm-1.62 6.277a4.7 4.7 0 0 0 .876.652c.304.174.91.469 1.818.886l-1.705 4.296H8.364l.942-5.834z"/>-->
<!--                    </g>-->
<!--                </svg>-->
<!--                -->
<!--                Responsibilities-->
<!--            </div>-->

<!--            <form>-->
<!--                <div class="form-group">-->
<!--                    &lt;!&ndash; Categories &ndash;&gt;-->
<!--                    <div class="job-title" style="margin-bottom:-18px">Category Title</div>-->

<!--                    <div class="form-group emp-row row-center" v-for="(to, catIndex) in responsibilities" :key="'qualItem' + catIndex">-->
<!--                        <div class="job-col-left">-->
<!--                            <input class="form-control" type="text" placeholder="e.g Quality Management" v-model="responsibilities[catIndex].title" @keyup="onTypeCategory(responsibilities[catIndex], catIndex)" @focus="onFocus('responsibilities')"/>-->
<!--                        </div>-->

<!--                        <div class="job-col-right">-->
<!--                            <span @click="removeEntity(catIndex, 'responsibilities')">-->
<!--                                <img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x" style="cursor:pointer">-->
<!--                            </span>-->
<!--                        </div>-->

<!--                        <div class="emp-row searched_list" style="margin-top:0" v-show="searchedResponsibilities && searchedResponsibilities.length > 0 && currentCatIndex == catIndex && focusTo == 'responsibilities'">-->
<!--                            <ul class="list-group">-->
<!--                                <li class="list-group-item" v-for="(res, idx) in searchedResponsibilities" :key="idx" @click="onSelectResponsibility(responsibilities[catIndex], res)">-->
<!--                                    {{ res }}-->
<!--                                </li>-->
<!--                            </ul>-->
<!--                        </div>-->
<!--                        &lt;!&ndash; Points &ndash;&gt;-->
<!--                        <div class="ml-4" style="width: 100%;">-->
<!--                            <div class="job-title" style="margin-bottom:-18px">Points</div>-->

<!--                            <div class="form-group emp-row row-center" v-for="(to, index) in responsibilities[catIndex].items" :key="'ptItem' + index">-->
<!--                                <div class="job-col-left">-->
<!--                                    <input class="form-control" type="text" placeholder="e.g Comply with and ensure project works are in accordance with Probuild QM Policies."-->
<!--                                        v-model="responsibilities[catIndex].items[index]" @focus="onFocus('points')" @keyup="onTypePoint(responsibilities[catIndex], responsibilities[catIndex].items[index], catIndex, index)" />-->
<!--                                </div>-->

<!--                                <div class="job-col-right">-->
<!--                                    <span @click="removeEntity(catIndex, 'items', index)">-->
<!--                                        <img src="/img/icons/remove.png" srcset="/img/icons/remove@2x.png 2x, /img/icons/remove@3x.png 3x" style="cursor:pointer">-->
<!--                                    </span>-->
<!--                                </div>-->

<!--                                <div class="emp-row searched_list" style="margin-top:0" v-show="searchedPoints && searchedPoints.length > 0 && currentCatIndex == catIndex && currentItemIndex == index">-->
<!--                                    <ul class="list-group">-->
<!--                                        <li class="list-group-item" v-for="(res, idx) in searchedPoints" :key="idx" @click="onSelectPoint(responsibilities[catIndex], index, res)">-->
<!--                                            {{ res }}-->
<!--                                        </li>-->
<!--                                    </ul>-->
<!--                                </div>-->
<!--                            </div>-->

<!--                            <div class="btn btn-link btn-delete" @click="addEntity(catIndex, 'items')" v-if="false">New Point</div>-->
<!--                        </div>-->
<!--                    </div>-->

<!--                    <div class="btn btn-link btn-delete" @click="addEntity(null, 'responsibilities')">Add new category</div>-->
<!--                </div>-->
<!--            </form>-->
<!--        </div>-->
<!--    </div>-->
</template>

<script>
    import Api from '@/api';

    export default {
        name: "new-job-responsibilities",
        data() {
            return {
                responsibilities: [],
                currentCatIndex: null,
                currentItemIndex: null,
                searchedResponsibilities: [],
                searchedPoints: [],
                timeout: null,
                focusTo: null
            }
        },
        created() {
            let vm = this;

            Bus.$on('newJobRequirements', function() {
                Bus.$emit('newJobResponsibilities', {
                    responsibilities: vm.responsibilities,
                });
            });

            Bus.$on('jobResponsibilitiesDetails', function(detailsArray) {
                if (detailsArray && detailsArray.length != 0) {
                    vm.responsibilities = detailsArray;
                }
            });

            Bus.$on('clearNewJobResponsibilities', () => {

                this.searchedResponsibilities = [];
                this.searchedPoints = [];
            });

            this.responsibilities.push({ title: '', items: [ '' ] });
        },
        methods: {

            onFocus(type) {

                console.log('NewJobResponsiblities');

                this.focusTo = type;

                Bus.$emit('clearNewJobDetails');
                Bus.$emit('clearNewJobRequirements');

                this.searchedResponsibilities = [];
                this.searchedPoints = [];
            },
            addEntity(index, field) {

                this.searchedResponsibilities = [];

                switch(field) {
                    case 'responsibilities':
                      //  this[field] = this[field].filter(r => r.title !== '');
                        this[field].push({ title: '', items: [ '' ] });
                        break;

                    case 'items':
                        this.responsibilities[index].items = this.responsibilities[index].items.filter(r => r !== '');
                        this.responsibilities[index].items.push('');
                        break;
                }
            },
            removeEntity(index, field, index2 = null) {

                this.searchedResponsibilities = [];

                switch(field) {
                    case 'responsibilities':
                        if (this[field].length > 1)  {
                            this[field].splice(index, 1);
                        }
                        break;

                    case 'items':
                        if (this.responsibilities[index].items.length > 1) {
                            this.responsibilities[index].items.splice(index2, 1);
                        }
                        break;
                }
            },
            async onTypeCategory(responsibility, catIndex) {

                let vm = this;

                this.currentCatIndex = catIndex;
                vm.searchedResponsibilities = [];

                if (this.timeout) {

                    clearTimeout(this.timeout);
                }

                if (responsibility.title && responsibility.title.length > 0) {

                    this.timeout = await setTimeout(() => {

                        Api.getCollectedJobResponsibilities(responsibility.title, 'byCategory').then((data) => {

                            if (data.data.responsibilities) {

                                vm.searchedResponsibilities = data.data.responsibilities;
                            }
                        });

                    }, 500)

                }

            },
            async onTypePoint(responsibility, currItem, catIndex, itemIndex) {

                let vm = this;

                responsibility.items = responsibility.items.filter(function (el) {
                    return el != '';
                });

                this.currentCatIndex = catIndex;
                this.currentItemIndex = itemIndex;

                vm.searchedPoints = [];

                if (this.timeout) {

                    clearTimeout(this.timeout);
                }

                if (currItem) {

                    this.timeout = await setTimeout(() => {

                        Api.getCollectedJobResponsibilities(currItem, 'byItems').then((data) => {

                            console.log(data);

                            if (data.data.responsibilities) {

                                vm.searchedPoints = data.data.responsibilities;
                            }
                        });

                        responsibility.items.push('');
                    }, 200);

                }

            },
            onSelectResponsibility(responsibility, selResponsibility) {

                this.searchedResponsibilities = [];
                responsibility.title = selResponsibility;
            },

            onSelectPoint(responsibility, itemIdx, selResponsibility) {

                this.searchedPoints = [];
                responsibility.items[itemIdx] = selResponsibility;
            }
        },
    }
</script>
