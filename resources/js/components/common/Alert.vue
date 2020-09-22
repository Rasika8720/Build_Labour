<template>
	<transition name="slide-fade">
		<div v-bind:class="alert" v-if="alert != 'hidden'">
			<div class="container" style="text-align: left;">
				<img v-bind:src="icon" class="alert-icon" />

				{{ message }} <a class="alert-link" v-if="resend" @click="resendEmail">resend</a>

				<img src="/img/icons/alert-close.png" class="alert-close" @click="hide" />
			</div>
		</div>
	</transition>
</template>

<script>
	export default {
		data() {
			return {
				message: '',
				alert: 'hidden',
				icon: '',
				resend: false,
				input_resend: {
					email: '',
				},
				endpoints: {
					resend: '/api/v1/auth/verification/resend',
				}
			}
		},

    	mounted() {
			let vm = this;

			Bus.$on('alertSuccess', function (message) {
				vm.message = message;
				vm.alert = 'alert-main alert-success alert-dismissible';
				vm.icon = '/img/icons/alert-success.png';
				vm.resend = false;
			});

			Bus.$on('alertError', function (message) {
				vm.message = message;
				vm.alert = 'alert-main alert-error alert-dismissible';
				vm.icon = '/img/icons/alert-error.png';
				vm.resend = false;
			});

			// Bus.$on('alertVerify', function (email) {
			// 	vm.message = 'Please verify your account. You may check your confirmation email or click ';
			// 	vm.alert = 'alert-main alert-info alert-dismissible';
			// 	vm.icon = '';
			// 	vm.resend = true;
			// 	vm.input_resend.email = email;
			// });

			Bus.$on('alertHide', function () {
				vm.hide();
			});
		},

		methods: {

			hide() {
				this.message = ''
				this.alert = 'hidden';
				this.icon = '';
				this.resend = false;
			},
			
			async resendEmail() {
				let vm = this;
				
				vm.resend = false;
				vm.hide();

				await axios.post(vm.endpoints.resend, vm.$data.input_resend, Utils.getBearerAuth())

				.then(function(response) {
					let data = response.data;
					
					vm.message = data.message;
					vm.alert = 'alert-main alert-success alert-dismissible';
					vm.icon = '/img/icons/alert-success.png';
					vm.resend = false;
				
				}).catch(function(error) {

					Utils.handleError(error);
				});
			},
		}
  	}
</script>