<template>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-2 mb-2">
                <img :src="photo" class="img-responsive">
            </div>
            <div class="col-md-6 mb-2">
                <input type="file" v-on:change="onFileChange" class="form-control">

                <button class="btn btn-primary" @click="uploadPhoto" :disabled="disabled">Upload</button>
            </div>
            <!-- <div class="col-md-2"> -->
                <!-- <button class="btn btn-primary" @click="uploadPhoto">Upload</button> -->
            <!-- </div> -->
        </div>
    </div>
</template>

<style scoped>
    img {
        max-height: 36px;
    }
</style>

<script>
    export default {
        data() {
            return {
                disabled: false,
                photo: '',
                endpoints: {
                    upload: '/api/v1/user/photo',
                },
            }
        },

        methods: {

            async uploadPhoto() {
                let vm = this;

                vm.disabled = true;

                await axios.post(vm.endpoints.upload, { photo: vm.photo }, Utils.getBearerAuth())

                .then(function(response) {
                    let data = response.data;

                    Bus.$emit('alertSuccess', data.message);
                
                }).catch(function(error) {
                    
                    Utils.handleError(error);
                });
                
                vm.disabled = false;
            },

            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;

                if (!files.length) return;

                this.createImage(files[0]);
            },

            createImage(file) {
                let vm = this;
                let reader = new FileReader();

                reader.onload = (e) => {
                    vm.photo = e.target.result;
                };

                reader.readAsDataURL(file);
            },

        },

    }
</script>