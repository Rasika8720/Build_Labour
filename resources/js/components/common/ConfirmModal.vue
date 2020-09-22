<template>
	<div id="confirmModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content modal-ku">
                <div class="modal-header" style="border-bottom:none">
					<!-- <h4 class="modal-title">&#9888; Warning! Empty or unsaved changes</h4> -->
                    <h4 class="modal-title">Confirm {{ action.charAt(0).toUpperCase() + action.substr(1) }}</h4>
					<div class="close" data-dismiss="modal">&times;</div>
				</div>
				
				<div class="modal-body mb-2">
                    <center>
                        <div class="mt-3" style="width:100%;font-size:16px;color:#000">
                            Are you sure you want to {{ action }} this record?
                        </div>
                    </center>
				</div>

				<div class="modal-footer" style="border-top:none">
					<div class="btn btn-link btn-delete" @click="proceed">Proceed</div>

                    <button class="pull-right" type="button" @click="cancel">Cancel</button>
				</div>
			</div>
        </div>
    </div>
</template>

<script>
	export default {
		data() {
			return {
                action: '',
			}
		},
		methods: {
            proceed() {
                $('#confirmModal').modal('hide');

                if (this.action == 'duplicate') {
                    Bus.$emit('duplicateJobPost');
                }
            },
            cancel() {
                $('#confirmModal').modal('hide');
            },
        },
        created() {
			let vm = this;

			Bus.$on('confirmAction', function(action) {
                vm.action = action;
			});
		},
	}
</script>

<style scoped>
    .modal-ku {
        width: 100%;
    }
</style>