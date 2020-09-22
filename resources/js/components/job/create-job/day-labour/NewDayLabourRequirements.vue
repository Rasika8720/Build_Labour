<template>
    <div class="profile-item-2">
        <div class="profile-content">
            <div class="profile-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <g fill="none" fill-rule="evenodd">
                        <path d="M0 0h23v23H0z"></path>
                        <path data-v-951e012c="" fill="#00aeef" fill-rule="nonzero" d="M10 3a1 1 0 0 1
                        1-1h1a1 1 0 0 1 1 1h2a1 1 0 0 1 0 2H8a1 1 0 1 1 0-2h2zM6.525 4A1.25 1.25 0 0
                        0 7.75 5.5h7.5A1.25 1.25 0 0 0 16.475 4H17a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2
                        0 0 1-2-2V6a2 2 0 0 1 2-2h.525zM6.5 7a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0
                        1-1V8a1 1 0 0 0-1-1h-1zm0 .5h1A.5.5 0 0 1 8 8v1a.5.5 0 0 1-.5.5h-1A.5.5 0 0 1 6
                        9V8a.5.5 0 0 1 .5-.5zm0 8.5a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0
                        0-1-1h-1zm0 .5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1A.5.5 0 0 1 6 18v-1a.5.5 0 0 1
                        .5-.5zm0-5a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-1a1 1 0 0 0-1-1h-1zm0
                        .5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5zm4-4a.5.5
                        0 1 0 0 1h6a.5.5 0 1 0 0-1h-6zm0 4.5a.5.5 0 1 0 0 1h6a.5.5 0 1 0 0-1h-6zm0 4.5a.5.5
                        0 1 0 0 1h6a.5.5 0 1 0 0-1h-6z"></path>
                    </g>
                </svg>
                Requirements
            </div>
            <form>
                <div class="form-group">
                    <div class="job-title mb-2">Transportation</div>

                    <div class="set-wrapper"
                         v-for="item in lists.transport"
                         :key="item.id">

                        <input type="checkbox"
                               class="styled-checkbox-round"
                               :ref="getSafeName(item.name)"
                               :id="getSafeName(item.name)"
                        />
                        <label :for="getSafeName(item.name)">{{item.name}}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="job-title mb-2">Clothing</div>

                    <div class="set-wrapper"
                         v-for="item in lists.clothing"
                         :key="item.id">

                        <input type="checkbox"
                               class="styled-checkbox-round"
                               :ref="getSafeName(item.name)"
                               :id="getSafeName(item.name)"
                        />
                        <label :for="getSafeName(item.name)">{{item.name}}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="job-title mb-2">Tools</div>

                    <div class="set-wrapper"
                         v-for="item in lists.tools"
                         :key="item.id">

                        <input type="checkbox"
                               class="styled-checkbox-round"
                               :ref="getSafeName(item.name)"
                               :id="getSafeName(item.name)"
                        />
                        <label :for="getSafeName(item.name)">{{item.name}}</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="job-title mb-2">Ability</div>

                    <div class="set-wrapper"
                         v-for="item in lists.work_ability"
                         :key="item.id">

                        <input type="checkbox"
                               class="styled-checkbox-round"
                               :ref="getSafeName(item.name)"
                               :id="getSafeName(item.name)"
                        />
                        <label :for="getSafeName(item.name)">{{item.name}}</label>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'new-day-labour-requirements',
        data() {
            return {
                requirements: {
                    tickets: [], clothing: [], transport: [], tools: [], work_ability: []
                },
                lists: {
                    clothing: [], transport: [], tools: [], work_ability: []
                }
            }
        },
        mounted() {
            let vm = this;
            this.getRequirements();
        },
        methods: {
            getRequirements() {
                let vm = this;

                axios.get('/api/v1/day-labour/requirements', Utils.getBearerAuth())
                    .then(function(response) {
                        let data = response.data.data;
                        let requirementsIndex = Object.keys(data);

                        requirementsIndex.forEach( function(item) {
                            let value = data[item],
                                indexSafeName = item.toLowerCase().replace(' ', '_');

                            vm.lists[indexSafeName] = value;
                        })
                    })
                    .catch(function(response) {
                        console.log(response);
                    });

                console.log(vm.lists);
            },
            getSafeName(name) {
               return name.toLowerCase().replace(' ', '_');
            }
        }
    }
</script>
