<template>
    <div class="form-group" v-if="show">
        <div class="record-title">
            {{ record.job_role_name }}
        </div>
        <div class="row" v-if="record.id != 0">
            <div class="col-md-12">
                <label class="record-label">ID</label>
                
                <input type="text" class="form-control record-input" v-model="record.id" disabled />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="record-label">Role Name</label>
                
                <input type="text" class="form-control record-input" v-model="record.job_role_name" />

                <span class="err-msg bl-ml-20" v-if="errors.job_role_name">
                    {{ errors.job_role_name }}
                </span>
            </div>
        </div>

        <div class="form-group row mt-5">
            <div class="col-md-12">
                <button type="submit" @click="submit" :disabled="disabled">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
		data() {
			return {
                disabled: false,
                show: false,
				record: {
                    id: 0, job_role_name: ''
                },
                errors: {
                    job_role_name: ''
                },
                job_roles: [],
				endpoints: {
                    get: '/api/v1/admin/job/roles/',
                    save: '/api/v1/admin/job/roles'
				}
			}
		},
		
		created() {
			let vm = this;

            Bus.$on('datatableViewJobRole', function(id){
                vm.show = true;
                
                if (id != 0) {
                    vm.endpoints.get = '/api/v1/admin/job/roles/' + id;

                    vm.viewRecord();
                
                } else {

                    Utils.setObjectValues(vm.record, '');

                }
            });

		},
		
		methods: {
			
		  	viewRecord() {
				let vm = this;

				axios.get(vm.endpoints.get, Utils.getBearerAuth())

                .then(function(response) {
                    
                    vm.record = response.data.data.role;

                }).catch(function(error) {
                    
                    Utils.handleError(error);
                });
            },

            onChange(event) {

                let findById = parseInt(event.target.value);

                let findRole = this.job_roles.find(x => x.id === findById);

                if (findRole) {

                    this.record.title = findRole.job_role_name;
                }
            },

            async submit() {

                let vm = this;

                Utils.setObjectValues(this.errors, '');

                this.disabled = true;

                await axios.post(vm.endpoints.save, vm.$data.record, Utils.getBearerAuth())

                .then(function(response) {

                    Bus.$emit('adminSaveChanges', vm.record.id);
                
                }).catch(function(error) {
                    let inputErrors = Utils.handleError(error);
    
                    if (inputErrors) vm.errors = inputErrors;
                });

                this.disabled = false;
            },
            
        }
	}
</script>