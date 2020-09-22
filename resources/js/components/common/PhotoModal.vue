<template>
  	<main-modal id="photoModal" data-backdrop="static" data-keyboard="false">
		  
		<template slot="custom-modal-title">
			<h4 class="modal-title">Crop Photo</h4>
			<div class="close" data-dismiss="modal" @click="close">&times;</div>
		</template>

		<template slot="custom-modal-content">
			<div class="row row-center" style="margin:0 auto">
                <div>
        			<img src="" id="crop_photo" />
				</div>
      		</div>
		</template>

		<template slot="custom-modal-footer">
            <button class="pull-right" type="button" @click="uploadPhoto" :disabled="disabled">Save Photo</button>
        </template>

	</main-modal>
</template>

<script>
	import Api from '@/api';
	import Cropper from 'cropperjs';
	import MainModal from '../common/MainModal';

	var cropper = null;

	export default {
		name: "photo-modal",
		data() {
			return {
				disabled: false,
				type: 'User',
				imgCrop: '',
				fileType: 'images/jpeg',
				input: {
					id: 0,
					photo: '',
				},
				endpoints: {
					upload: '',
				},
			}
		},

		created() {
			let vm = this;

			$('#photoModal').on('hidden.bs.modal', function () {
				$('#upload').val('');

				vm.close();
			});

			Bus.$on('imageToCrop', function (binary, id, type, fileType) {
				vm.type = type;
				
				if (type == 'Admin') {
					vm.input.id = id;
					vm.endpoints.upload = '/api/v1/admin/user/upload';
				
				} else if (type == 'User') {
					vm.endpoints.upload = '/api/v1/user/photo';
				
				} else if (type == 'Company') {
					vm.endpoints.upload = '/api/v1/company/photo';
				
				} else if (type == 'CompanyRegister') {
					vm.endpoints.upload = '';
				}

				vm.imgCrop = binary;
				vm.fileType = fileType;
				vm.enableCropper();
				
				$('#photoModal').modal('show');
			});
		},
		
		methods: {

			enableCropper() {
				$('#crop_photo').attr('src', this.$data.imgCrop);

				if (cropper) {
					cropper.destroy();
				}

				let image = document.getElementById('crop_photo'),
					modalWidth = $('#photoModal').width(),
					containerWidth = (modalWidth > 465 || (modalWidth < 280)) ? 465 : modalWidth;

				cropper = new Cropper(image, {
					viewMode: '1',
					dragMode: 'move',
					aspectRatio: 16 / 16,
					autoCropArea: 0.8,
					restore: false,
					guides: false,
					center: false,
					highlight: false,
					minContainerWidth: containerWidth,
					minContainerHeight: containerWidth,
				});
			},

			close() {
				if (cropper) {
					cropper.destroy();
				}

				$('#crop_photo').attr('src', '');
				$('#photoModal').modal('hide');

				Bus.$emit('closePhotoModal');
			},

			async uploadPhoto() {
				let vm = this;

				this.input.photo = cropper.getCroppedCanvas().toDataURL(this.fileType, (20 / 100));
				
				this.disabled = true;

				if (this.type != 'CompanyRegister') {

					await axios.post(vm.endpoints.upload, vm.$data.input, Utils.getBearerAuth())
				
					.then(function(response) {
						let data = response.data
						
						if (data.success) {
							vm.close();

							if (vm.type == 'User') {
								Api.setNavAvatar('', data.data.user.profile_photo_url);
								Api.redirectToProfile();
							
							} else if (vm.type == 'Company') {
								Api.setNavAvatar('', data.data.photo_url.photo_url);
								Api.redirectToProfile();
								
							} else {
								Bus.$emit('alertSuccess', data.message);
								Bus.$emit('croppedPhoto', data.data.user.profile_photo_url);
							}
						}
					
					}).catch(function(error) {

						Utils.handleError(error);
					});	
				
				} else {
					this.close();
					
					Bus.$emit('croppedPhoto', this.input.photo);
				}
				
				this.disabled = false;
			},
		},
		components: {
			MainModal,
		},
	}
</script>

<style scoped>
	.cropper-view-box, .cropper-face {
		border-radius: 50%;
	}
	.cropper-container {
		margin:0 auto;
	}
</style>