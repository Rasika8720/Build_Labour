<template>
    <div>
        <fieldset>
            <legend><h1>Upload Json File</h1></legend>   
            <div class="form-group">
                <div class="input-group mb-3">
                    <div class="custom-file">
                        <label class="custom-file-label" for="inputGroupFile02">{{ label }}</label>
                        <input type="file" name="fileInput" class="custom-file-input" id="inputGroupFile02" @change="onFileChange" required>
                    </div>
                    <div class="input-group-append">
                        <button class="input-group-text" @click="addJson" :disabled="file==''||name==''">Upload</button>
                    </div>
                </div>

                <div class="text-danger" v-if="error">
                    {{ error }}
                </div>
                <div class="text-success" v-if="success">
                    {{ success }}
                </div>
                <!-- <a v-if="filereturn" :href='filereturn' target="_blank" download>Download File</a> -->
            </div>
        </fieldset>
    </div>
</template>
<script>
import Axios from 'axios'
export default {
    name: "upload-json",
    data() {
        return{
            file:'',
            jsonData:'',
            error:'',
            success:'',
            label:'Choose File',
            filereturn:''
        }
    },
    methods: {
        onFileChange(e) {
            this.error=this.success=this.filereturn='';
            var files = e.target.files || e.dataTransfer.files;
            if (files.length){
                this.file = files.item(0);
                this.label = files.item(0).name
                const fr = new FileReader();
                fr.onload = e => {
                    this.jsonData = e.target.result;
                    console.log(this.jsonData)
                }
                fr.readAsText(files.item(0));
            }
        },
        addJson() {
            let formData = new FormData()
            if(this.file!='') {
                formData.append('file', this.file)
                formData.append('jsonData', this.jsonData)
                Axios
                    .post('/api/uploadJson', formData )
                    .then((response) => {
                        this.error=this.success=this.filereturn='';
                        if(response.data.status=='error'){
                            this.error = response.data.msg;
                        }else{
                            this.success=response.data.msg;
                            this.filereturn=response.data.viewFile;
                        }
                    })
                    .catch((error) => {
                        if( error.response ){
                            this.error=this.success=this.filereturn='';
                            this.error = error.response.data.message;
                        }
                    });
            }
            
        },
    },
}
</script>
<style>
    
</style>