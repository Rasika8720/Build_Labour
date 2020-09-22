<template>
    <main-modal id="modalTier">
        <template slot="custom-modal-content"><div id="talkbubble"></div>
            <div class="row tier-main-container">
                <div class="col-md-11 tier-container">
                    <div class="row tier-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 tier-name-container">
                                    <div class="tier-name">
                                        <span>Tier 1</span>                                        
                                    </div>
                                    
                                </div>
                                <div class="col-md-10 tier-description-container">
                                        <span class="tier-description">Tier 1 construction companies work on the largest and most significant infrastructure projects, capable of completing a $1 Billion build project.</span>
                                </div>                        
                            </div>
                        </div>
                    </div>
                    <div class="row tier-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 tier-name-container">
                                    <div class="tier-name">
                                        <span>Tier 2</span>
                                    </div>
                                </div>
                                <div class="col-md-10 tier-description-container">
                                        <span class="tier-description">Tier 2 focus on large scale commercial projects and small to mid sized infrastructure projects. Average project values exceeding $70 million.</span>
                                </div>                        
                            </div>
                        </div>
                    </div>
                    <div class="row tier-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 tier-name-container">
                                    <div class="tier-name">
                                        <span>Tier 3</span>
                                    </div>
                                </div>
                                <div class="col-md-10 tier-description-container">
                                        <span class="tier-description">Tier 3 work on smaller scale projects with average project values between $1 - 40 million.</span>
                                </div>                        
                            </div>
                        </div>
                    </div>
                    <div class="row tier-row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-2 tier-name-container">
                                    <div class="tier-name">
                                        <span>Tier 4</span>
                                    </div>
                                </div>
                                <div class="col-md-10 tier-description-container">
                                    <span class="tier-description">Residential Construction projects ranging from $500k $4million</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>            
        </template>        
        <template slot="custom-modal-footer">
            <div class="row">
                <div class="close-modal" data-dismiss="modal">
                    <div class="close-modal-text">
                        <span> Got it </span> 
                    </div>                    
                </div>
            </div>
        </template>
    </main-modal>      
</template>

<script>
import MainModal from '../common/MainModal';

export default {
    name: "tier-modal",
    components:{
        MainModal
    }    
}
</script>

<style Scoped>
    .modal-dialog{

        width:1250px !important;
        height: 437px;
        border-radius: 8px;
        box-shadow: 0 0 0.5px 0 rgba(33, 49, 60, 0.08), 0 3px 19px 0 rgba(38, 52, 64, 0.09);
        background-color: #ffffff;  
    }
    .modal-header{
        border-bottom: none !important;
    }
    .tier-name-container{                
        border-radius: 8px 0 0 8px;
        background-color: #00aeef;
        
    }
    .tier-name{
        margin-top: 30px;
        width: 100%;
        height: 18px;
        font-family: Inter;
        font-size: 15px;
        font-weight: bold;
        font-style: normal;
        font-stretch: normal;
        line-height: normal;
        letter-spacing: normal;
        text-align: center;        
        color: #ffffff;        
    }
    .tier-description-container{
        padding: 11px 10px 5px 10px !important;
        width: 428px;        
        border-radius: 0 8px 8px 0;
        background-color: #383d3f;        
        word-wrap: break-word;        
    }
    .tier-description{
        padding-top: 0 !important;
        width: 392px;
        height: 66px;
        font-family: Inter;
        font-size: 15px;
        font-weight: 500;
        font-style: normal;
        font-stretch: normal;
        line-height: normal;
        letter-spacing: normal;
        color: #ffffff;
        padding-left: 5px;
        
    }   
    .tier-container{
        margin: 0 auto;
    } 
    .tier-row{
        padding-bottom: 20px;
        height: auto;
    }
    .tier-main-container{
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    .modal-footer {
        border-top: 0 none !important;
    }
    .close-modal{
        width: 135px;
        height: 42px;
        border-radius: 8px;
        background-color: #00aeef;
        margin: 0 auto;
        display : flex;
        align-items : center;
        justify-content: center;
    }
    .close-modal-text{
        
        height: 22px;
        font-family: Inter;
        font-size: 15px;
        font-weight: 500;
        font-style: normal;
        font-stretch: normal;
        line-height: normal;
        letter-spacing: normal;
        text-align: center;
        color: #ffffff;
    }
    .close-modal:hover{
        cursor: pointer;
    }
  

    .tier-name-container:before {
      content: "";
      position: absolute;
      left: 100%;
      top: 26px;
      width: 0;
      height: 0;
      border-top: 12px solid transparent;
      border-left: 10px solid #00aeef;
      border-bottom: 12px solid transparent;
      z-index: 1;
    }

    .modal-content{
        border: none !important;
    }
</style>