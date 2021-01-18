<template>
    <div style="max-width:95vw;">
        <div class="row">
            <div class="col-auto mr-auto">
                <h1 class="text-center text-primary font-weight-bold">Json Details</h1>
            </div>
            <div class="col-auto" v-if="status||uploads.status=='2'">
                    <button type="button" class="btn btn-success" disabled>Approved</button>
            </div>
            <div class="col-auto"  v-else>
                    <button type="button" class="btn btn-success" :disabled='selected==""' @click="UploadAdsApprove(uploads.id)">Approve</button>
            </div>
        </div>
        <!-- <div class="overflow-auto mb-5" style="min-height:5vh;">
            <table class="table table-hover">
                <thead class="thead-info">
                    <tr class="table-info">
                        <th scope="col" class="font-weight-bold">Title</th>
                        <th scope="col" class="font-weight-bold">Description</th>
                        <th scope="col" class="font-weight-bold">About</th>
                        <th scope="col" class="font-weight-bold">exp_level</th>
                        <th scope="col" class="font-weight-bold">Contract_type</th>
                        <th scope="col" class="font-weight-bold">Salary</th>
                        <th scope="col" class="font-weight-bold">Salary_type</th>
                        <th scope="col" class="font-weight-bold">Project_size</th>
                        <th scope="col" class="font-weight-bold">Location</th>
                        <th scope="col" class="font-weight-bold">Company_name</th>
                        <th scope="col" class="font-weight-bold">Job_role</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-light" v-for="item in uploadAds" :key='item.id'>
                        <td scope="row">{{ item.title }}</td>
                        <td>{{ item.description }}</td>
                        <td>{{ item.about }}</td>
                        <td>{{ item.exp_level }}</td>
                        <td>{{ item.contract_type }}</td>
                        <td>{{ item.salary }}</td>
                        <td>{{ item.salary_type }}</td>
                        <td>{{ item.project_size }}</td>
                        <td>{{ item.location }}</td>
                        <td>{{ item.company_name }}</td>
                        <td>{{ item.job_role }}</td>
                    </tr>
                </tbody>
            </table>
        </div> -->
        <div class="overflow-auto" style="min-height:0vh;">
            <table class="table table-hover">
                <thead class="thead-info">
                    <span v-if="selected.length>0"> {{selected.length}} Selected</span>
                    <tr class="table-info">
                        <td scope="col" class="text-muted"><input type="checkbox" v-model="selectAll" @click="allSelect"></td>
                        <th scope="col" class="font-weight-bold">Title</th>
                        <th scope="col" class="font-weight-bold">Company</th>
                        <th scope="col" class="font-weight-bold">Location</th>
                        <th scope="col" class="font-weight-bold">Status</th>
                        <th scope="col" class="font-weight-bold"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-light" v-for="item in uploadAds" :key='item.id'>
                        <td><input type="checkbox" :value="item.id" v-model="selected" @click="selectOne" :disabled='item.status==1'></td>
                        <td scope="row">{{ item.title }}</td>
                        <td>{{ item.company_name }}</td>
                        <td>{{ item.location }}</td>
                        <td v-if="item.status==1" class="text-danger">Error</td>
                        <td v-if="item.status==2" >Passed</td>
                        <td v-if="item.status==3">Modified</td>
                        <td><button type="button" class="btn btn-success" @click="UploadAdsApproveEdit(item.id)">Edit</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import Axios from 'axios'
export default {
    name: "upload-ads",
    data() {
        return {
            uploadAds:'',
            status:'',
            error:'',
            success:'',
            fileName:'',
            uploads:'',
            selected:[],
            selectAll:false,
        }
    },
    props:{
        ad_id:Object,
        upload:Object,
    },
    methods: {
        UploadAdsApprove(id){
            this.uploads='';
            Axios
            .post('/api/uploadAdsApprove/'+ id,{selected:this.selected})
            .then((response) => {
                this.error=this.success='';
                this.status=response.data.text;
                this.success=response.data.msg;
                console.log(this.success);
            })
            .catch((error) => {
                if( error.response ){
                    this.error=this.success='';
                    this.error = error.response.data.message;
                    console.log(this.error);
                }
            });
        },
        UploadAdsApproveEdit(id){
            window.location.href = '/api/UploadAdsApproveEdit/' + id;
        },
        allSelect() {
            this.selected = [];
            if (!this.selectAll) {
                this.uploadAds.forEach(item => {
                    this.selected.push(item.id);
                });
            }
            console.log(this.selected)
        },
        selectOne() {
            this.selectAll=false
        },
    },
    created() {
        this.uploadAds=this.ad_id
        this.uploads=this.upload
    },
}
</script>