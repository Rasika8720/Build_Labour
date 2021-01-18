<template>
    <div>
        <div class="row">
            <div class="col-auto mr-auto">
                <h1 class="text-center text-primary font-weight-bold">Upload Files</h1>
            </div>
            <div class="col-auto">
                    <button type="button" class="btn btn-secondary" @click="UploadJsonView()">Upload Json File</button>
            </div>
        </div>

        <table class="table table-hover">
            <thead>
                <tr class="table-info">
                    <th scope="col" class="font-weight-bold">File Name</th>
                    <th scope="col" class="font-weight-bold">Uploded date</th>
                    <th scope="col" class="font-weight-bold">Actions</th>
                    <th scope="col" class="font-weight-bold">Status</th>
                </tr>
            </thead>
            <tbody>
                
                <tr class="table-secondary" v-for="item in uploads" :key='item.id'>
                    <td scope="row">{{ item.file_name }}</td>
                    <td>{{ item.uploaded_date }}</td>
                    <td><button type="button" class="btn btn-primary" @click="UploadAdsView(item.id)">View</button></td>
                    <!-- <td><button type="button" class="btn btn-success" @click="UploadAdsApprove(item.id)">Approve</button></td> -->
                    <td v-if="item.status==1">New</td>
                    <td v-if="item.status==2">Completed</td>
                </tr>
                
            </tbody>
        </table>
    </div>
</template>
<script>
import Axios from 'axios'
export default {
    name: "grid-jobs",
    data() {
        return {
            uploads:'',
            error:'',
            success:'',
            fileName:'',
        }
    },
    created() {
        //
    },
    methods: {
        getUpdateValues(){
            this.uploads='';
            Axios
            .get('/user/GetUploadsValus')
            .then(response => (this.uploads = response.data))
        },
        UploadJsonView(){
            window.location.href = '/user/UploadJson';
        },
        UploadAdsView(id){
            window.location.href = '/user/UploadAds/' + id;
        },
        UploadAdsApprove(id){
            this.uploads='';
            Axios
            .get('/api/uploadAdsApprove/'+ id)
            .then((response) => {
                        this.error=this.success=this.fileName='';
                        this.success=response.data.msg;
                        this.fileName=response.data.fileName;
                        this.getUpdateValues();
                        console.log(this.success);
                        console.log(response.data.fileName);
                    })
            .catch((error) => {
                        if( error.response ){
                            this.error=this.success='';
                            this.error = error.response.data.message;
                            this.getUpdateValues();
                            console.log(this.error);
                        }
                    });
        },
    },
    created() {
        this.getUpdateValues()
        
    },
}
</script>
<style>
    
</style>