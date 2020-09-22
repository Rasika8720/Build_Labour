<template>
    <div class="form-group" v-if="show">
        <delete-modal></delete-modal>

        <div class="record-title">
            {{ record.ticket }}
        </div>
        <div class="row" v-if="record.id != 0">
            <div class="col-md-12">
                <label class="record-label">ID</label>
                
                <input type="text" class="form-control record-input" v-model="record.id" disabled />
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="record-label">TICKET</label>
                
                <input type="text" class="form-control record-input" v-model="record.ticket" />

                <span class="err-msg bl-ml-20" v-if="errors.ticket">
                    {{ errors.ticket }}
                </span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <label class="record-label">DESCRIPTION</label>
            </div>

            <div class="col-md-12">
                <textarea class="record-textarea" style="height:180px;border:1px solid #ced4da;" v-model="record.description"></textarea>
            </div>

            <span class="err-msg bl-ml-30" style="margin-top:-6px;" v-if="errors.description">
                {{ errors.description }}
            </span>
        </div>

        <div class="form-group row mt-5">
            <div class="col-md-12">
                <div class="btn btn-link btn-delete mb-3" style="margin-right:180px" data-dismiss="modal" @click="deleteRecord">
                    Delete
                </div>
                
                <button type="submit" @click="submit" :disabled="disabled">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</template>

<script>
    import DeleteModal from '../common/DeleteModal';

    export default {
        name: "view-ticket",
		data() {
			return {
                disabled: false,
                show: false,
				record: {
                    id: 0, ticket: '', description: '',
                },
                errors: {
                    ticket: '', description: '',
                },
				endpoints: {
                    get: '',
                    save: '',
                    delete: '/api/v1/worker/ticket/',
				}
			}
		},
		created() {
			let vm = this;

            Bus.$on('datatableViewTicket', function(id){
                vm.show = true;

                if (id != 0) {
                    vm.endpoints.get = '/api/v1/admin/ticket/get?id=' + id;
                    vm.endpoints.save = '/api/v1/user/ticket/' + id;
                    vm.viewRecord();
                
                } else {
                    Utils.setObjectValues(vm.record, '');
                    vm.record.id = 0;
                    vm.endpoints.save = '/api/v1/user/ticket';
                }
            });

            Bus.$on('removeTicket', function() {
                Bus.$emit('adminSaveChanges', vm.record.id);
            });
		},
		methods: {
		  	viewRecord() {
				let vm = this;

				axios.get(vm.endpoints.get, Utils.getBearerAuth())

                .then(function(response) {
                    
                    vm.record = response.data.data.record;
                
                }).catch(function(error) {
                    
                    Utils.handleError(error);
                });
            },
            deleteRecord() {
                $('#deleteRecordModal').modal('show');

                Bus.$emit('deleteTicket', this.endpoints.delete + this.record.id);
            },
            async submit() {
                let vm = this;

                Utils.setObjectValues(vm.errors, '');

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
        },
        components: {
            DeleteModal,
        },
	}
</script>