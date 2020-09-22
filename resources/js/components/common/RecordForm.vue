<template>
	<main-modal :id="'modal' + title">
		
		<template slot="custom-modal-title">
			<h4 class="modal-title">{{ title.replace(/([A-Z])/g, ' $1').trim() }}</h4>
			<div class="close" data-dismiss="modal" @click="close">&times;</div>
		</template>

		<template slot="custom-modal-content">
			<form class="modal-form" method="POST" @submit.prevent="submit">
				<div class="form-group bl-full-row" v-for="(value, key) in record" :key="key.id">
					<div class="modal-form-label">
						{{ getKeyName(key) }}
					</div>

					<input class="form-control" :id="key" :name="key" type="text" v-model="input[key]" v-if="! textArea.includes(key)" />
					
					<textarea class="form-control" v-model="input[key]" v-else-if="textArea.includes(key)"></textarea>
					
					<span class="err-msg" v-if="errors[key]">
						{{ errors[key] }}
					</span>
				</div>
			</form>
		</template>

		<template slot="custom-modal-footer">
			<!-- <div class="btn btn-link btn-delete" data-dismiss="modal" v-if="current != -1" @click="deleteRecord">
                Delete
            </div> -->

            <button class="pull-right" type="submit" @click="submit" :disabled="disabled">
                Save Changes
            </button>
		</template>

	</main-modal>
</template>

<script>
	export default {
		data() {
			return {
				disabled: false,
				inputted: {},
				errors: {
					ticket: '', description: '',
				},
                textArea: [
					'introduction'
				],
				format: 'd MMMM yyyy',
            }
		},
        
        props: {
            title: {
                type: String,
                required: true
            },
            record: {
                type: Object,
                required: true
			},
			saveEndpoint: {
				type: String,
				required: true
			},
        },
        
		computed: {
			input() {
				return this.record;
			}
        },
		
		methods: {

			getKeyName(key) {
				return key.split('_').map(function(word) {
					return word.charAt(0).toUpperCase() + word.slice(1);
				}).join(' ');
			},

			close() {
                Utils.setObjectValues(this.errors, '');
			},
			
			async submit() {
                let vm = this;

				Utils.setObjectValues(vm.errors, '');
				vm.inputted = vm.input;
                vm.disabled = true;

                await axios.post(vm.saveEndpoint, vm.$data.inputted, Utils.getBearerAuth())
                    
				.then(function(response) {
					let data = response.data;
					
					Bus.$emit(vm.title, vm.inputted);
					
					$('#modal' + vm.title).modal('hide');
				
				}).catch(function(error) {
					let inputErrors = Utils.handleError(error);
        
                    if (inputErrors) vm.errors = inputErrors;
				});
                
                vm.disabled = false;
            },
	 	}
	}
</script>
