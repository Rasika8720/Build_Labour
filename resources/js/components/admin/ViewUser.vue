<template>
    <div class="form-group" v-if="record">
        <div class="record-title">
            {{ record.full_name }}
        </div>
        <ul class="record-links">
            <li class="active">Info</li>
            <li class="inactive">Chats</li>
            <li class="inactive">Something</li>
        </ul>
        <div class="record-photo">
            <div class="record-profile-pic">
                <img v-if="record.profile_photo_url" :src="record.profile_photo_url" height="120px" />
                <img v-else src="/img/defaults/user.png" height="120px" />
            </div>
            
            <input type="file" id="upload" value="Choose a file" accept="image/*" style="display:none" @change="onFileChange" />
            
            <div class="record-photo-actions">
                <div class="action-item" @click="onClickUploadImage">
                    <span class="fa fa-upload bl-ml-35 bl-mr-15 bl-mb-32"></span>
                    Upload Image
                </div>
                <div class="action-item" data-toggle="modal" data-target="#deleteRecordModal" @click="onClickDeleteImage">
                    <span class="fa fa-trash bl-ml-35 bl-mr-15"></span>
                    Delete Image
                </div>
            </div>
        </div>
        <div class="row" v-for="(value, key) in record" :key="key.id">
            <div class="col-md-12" v-if="key != 'profile_photo_url'">
                <label class="record-label">
                    {{ key.split('_').join(' ').toUpperCase() }}
                </label>
            </div>

            <div class="col-md-12" v-if="key != 'profile_photo_url'">
                <span v-if="key == 'created_at' || key == 'updated_at' || key == 'deleted_at'"
                    class="form-control record-input">{{ formatDate(value) }}</span>
                
                <span v-else-if="key != 'message'" class="form-control record-input">{{ value }}</span>

                <textarea v-else-if="key == 'message'" class="form-control record-input" :value="value" readonly></textarea>
            </div>
        </div>
        <!-- <div class="form-group row mt-5">
            <div class="col-md-12">
                <button class="btn btn-primary" data-toggle="modal" data-target="#deleteRecordModal" @click="onClickDeleteAccount()">
                    Delete Account
                </button>
            </div>
        </div> -->
    </div>
</template>

<script>
    export default {
        name: "view-user",
		data() {
			return {
                record_id: 0,
				record: null,
				endpoints: {
					get: ''
				}
			}
		},
		created() {
			let vm = this;

            Bus.$on('datatableViewUser', function(id){
                vm.record_id = id;
                vm.endpoints.get = '/api/v1/admin/user/get?id=' + id;
                vm.viewRecord();
            });
            
            Bus.$on('croppedPhoto', function (profile_photo_url) {
                vm.record.profile_photo_url = profile_photo_url;
            });

            Bus.$on('closePhotoModal', function() {
                $('#upload').val('');
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
            formatDate(d) {
                if (d != null) {
                    let date = new Date(d);

                    return date.getDate() + ' ' + Utils.getMonth(date.getMonth()) + ' ' + date.getFullYear();
                }
			},
            onClickDeleteAccount() {
				Bus.$emit('datatableDeleteUser', this.record_id);
            },
            onClickUploadImage() {
                upload.click();
            },
            onFileChange(e) {
                Utils.onFileChange(e, this.record_id, 'Admin');
            },
            onClickDeleteImage() {
                Bus.$emit('deletePhoto', this.record_id);
            },
        }
	}
</script>