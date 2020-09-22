<template>
	<transition name="slide-fade">
		<div v-bind:class="alert" v-if="alert != 'hidden'">
			<div class="container" style="text-align: left; padding: 0; margin: 0">
				<img v-bind:src="icon" class="alert-icon" />
					{{ message }}
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

			Bus.$on('alertSuccessEmployment', function (message) {
				vm.message = message;
				vm.alert = 'alert-main alert-success alert-dismissible';
				vm.icon = '/img/icons/alert-success.png';
				vm.resend = false;
			});

			Bus.$on('alertErrorEmployment', function (message) {
				vm.message = message;
				vm.alert = 'alert-main alert-error alert-dismissible';
				vm.icon = '/img/icons/alert-error.png';
				vm.resend = false;
			});

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
		}
  	}
</script>