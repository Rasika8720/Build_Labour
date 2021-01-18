<template>
    <div>
        <form @submit.prevent="UploadAdsUpdate">
            <fieldset>
                <legend class="mb-4">{{uploadAds.title}}</legend>
                <div class="row">
                    <div class="card mb-2 rounded float-left">
                          <img v-bind:src="uploadAds.job_logo" alt="job_logo" class="img-thumbnail">
                    </div>
                </div>
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" v-model="uploadAds.title" required>
                </div>
                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" class="form-control" id="company_name" v-model="uploadAds.company_name" required>
                </div>
                <div class="form-group">
                    <label for="description">Details</label>
                    <textarea class="form-control" id="description" rows="4" v-model="uploadAds.description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="about">About</label>
                    <textarea class="form-control" id="about" rows="3" v-model="uploadAds.about" required></textarea>
                </div>
                <div class="form-group">
                    <label for="contract_type">Contract Type</label>
                    <input type="text" class="form-control" id="contract_type" v-model="uploadAds.contract_type" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" class="form-control" id="location" v-model="uploadAds.location" required>
                </div>
                <div class="form-group">
                    <label for="salary">Salary</label>
                    <input type="text" class="form-control" id="salary" v-model="uploadAds.salary" required>
                </div>
                <div class="form-group">
                    <label for="job_role">Job Role</label>
                    <input type="text" class="form-control" id="job_role" v-model="uploadAds.job_role" required>
                </div>
                <div class="form-group">
                    <label for="job_url">Job Url</label>
                    <input type="text" class="form-control" id="job_url" v-model="uploadAds.job_url" required>
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
                <button type="submit" class="btn btn-secondary" @click="UploadAdsView(uploadAds.upload_id)">Cancel</button>
                <div class="text-danger float-right" v-if="error">
                    {{ error }}
                </div>
                <div class="text-success float-right" v-if="success">
                    {{ success }}
                </div>
            </fieldset>
        </form>
    </div>
</template>
<script>
import Axios from 'axios'
export default {
    name: "upload-edit",
    data() {
        return {
            uploadAds:'',
            error:'',
            success:'',
            fileName:'',
        }
    },
    props:{
        ad_id:Object,
    },
    methods: {
        UploadAdsUpdate(){
            Axios
            .post('/api/uploadAdsUpdate/', this.uploadAds)
            .then((response) => {
                        this.error=this.success=this.fileName='';
                        this.success=response.data.msg;
                        this.fileName=response.data.UploadAdsUpdated;
                        console.log(this.fileName);
                        console.log(this.success);
                        this.UploadAdsView(response.data.UploadAdsUpdated.upload_id)
                    })
            .catch((error) => {
                        if( error.response ){
                            this.error=this.success=this.fileName='';
                            this.error = error.response.data.message;
                            console.log(this.error);
                        }
                    });
        },
        UploadAdsView(id){
            window.location.href = '/user/UploadAds/' + id;
        },
    },
    created() {
        this.uploadAds=this.ad_id[0]
        console.log(this.uploadAds.title)
    },
}
</script>