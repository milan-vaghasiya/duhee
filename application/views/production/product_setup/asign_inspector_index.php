<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTabChange('setupReqTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTabChange('setupReqTable',1);" class=" btn waves-effect waves-light btn-outline-info" style="outline:0px" data-toggle="tab" aria-expanded="false"> Assigned </button> </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4 class="card-title">Setup Request</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='setupReqTable' class="table table-bordered ssTable" data-url='/getSetupDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="asign_inspector" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title">Material Request</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="asign_insp_form"> 
                    <input type="hidden" id="id" name="id">
                    <div class="col-md-12 form-group">
                        <label for="qci_id">Inspector</label>
                        <select name="qci_id" id="qci_id" class="form-control single-select">
                            <option value="">Select Inspector</option>
                            <?php
                            if(!empty($inspectorList)){
                                foreach($inspectorList as $row){
                                    ?>
                                    <option value="<?=$row->id?>"><?=$row->emp_name?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                        <div class="error qci_id"></div>
                    </div>
                </form>
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary save-form" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success save-form" onclick="store('asign_insp_form','saveAsignedInspector');"><i class="fa fa-check"></i> Send</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
    function asignInspector(data) {
        $("#asign_inspector").modal();
        $('#asign_insp_form')[0].reset();
        $("#id").val(data.id);
        $(".single-select").comboSelect();
    }

    function statusTabChange(tableId,status,srnoPosition=1){
        $("#"+tableId).attr("data-url",'/getSetupDTRows/'+status);
        ssTable.state.clear();initTable(srnoPosition);
    }
</script>