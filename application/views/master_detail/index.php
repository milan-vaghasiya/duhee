<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8 ">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="typeTab('masterDetailTable',1);" class="nav-link btn waves-effect waves-light btn-outline-info active" style="border-radius:0px;" data-toggle="tab" aria-expanded="false">Industry Type</button> </li>
                                    <li class="nav-item"> <button onclick="typeTab('masterDetailTable',2);" class="nav-link btn waves-effect waves-light btn-outline-info" style="border-radius:0px;" data-toggle="tab" aria-expanded="false">Firm Type</button> </li>
                                    <li class="nav-item"> <button onclick="typeTab('masterDetailTable',3);" class="nav-link btn waves-effect waves-light btn-outline-info" style="border-radius:0px;" data-toggle="tab" aria-expanded="false">QC Stage</button> </li>    
                                    <li class="nav-item"> <button onclick="typeTab('masterDetailTable',4);" class="nav-link btn waves-effect waves-light btn-outline-info" style="border-radius:0px;" data-toggle="tab" aria-expanded="false">Class</button> </li>
                                    <li class="nav-item"> <button onclick="typeTab('masterDetailTable',5);" class="nav-link btn waves-effect waves-light btn-outline-info" style="border-radius:0px;" data-toggle="tab" aria-expanded="false">Party Docs</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-primary float-right addNew iType" data-button="both" data-modal_id="modal-lg" data-type=1 data-function="addMasterDetail/1" data-form_title="Add Industry type"><i class="fa fa-plus"></i> Add Industry Type</button>
                                <button type="button" style="display:none;" class="btn btn-outline-primary float-right addNew fType" data-button="both" data-modal_id="modal-lg" data-type=2 data-function="addMasterDetail/2" data-form_title="Add Firm Type"><i class="fa fa-plus"></i> Add Firm Type</button>
                                <button type="button" style="display:none;" class="btn btn-outline-primary float-right addNew sType" data-button="both" data-modal_id="modal-lg" data-type=3 data-function="addMasterDetail/3" data-form_title="Add QC Stage Detail" ><i class="fa fa-plus"></i> Add QC Stage</button>
                                <button type="button" style="display:none;" class="btn btn-outline-primary float-right addNew cType" data-button="both" data-modal_id="modal-lg" data-type=3 data-function="addMasterDetail/4" data-form_title="Add Class Detail" ><i class="fa fa-plus"></i> Add Class</button>
                                <button type="button" style="display:none;" class="btn btn-outline-primary float-right addNew pType" data-button="both" data-modal_id="modal-lg" data-type=5 data-function="addMasterDetail/5" data-form_title="Add Party Docs" ><i class="fa fa-plus"></i> Add Party Docs</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='masterDetailTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){       
       
});
function typeTab(tableId,status){
    $(".iType").hide();$(".fType").hide();$(".sType").hide();
    if(status == 1){ 
        $(".iType").show();$(".fType").hide();$(".sType").hide();$(".cType").hide();$(".pType").hide()
    }else if(status == 2){
        $(".iType").hide();$(".fType").show();$(".sType").hide();$(".cType").hide();$(".pType").hide()
    }else if(status == 3){
        $(".iType").hide();$(".fType").hide();$(".sType").show();$(".cType").hide();$(".pType").hide()
    }else if(status == 4){
        $(".iType").hide();$(".fType").hide();$(".sType").hide();$(".cType").show();$(".pType").hide()
    }else{
        $(".iType").hide();$(".fType").hide();$(".sType").hide();$(".cType").hide();$(".pType").show()
    }
    $("#"+tableId).attr("data-url",'/getDTRows/'+status);
    ssTable.state.clear();initTable();
}
</script>