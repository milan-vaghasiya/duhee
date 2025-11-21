<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-pills">
                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/0")?>"  class="nav-link btn waves-effect waves-light btn-outline-info mr-1" >Pending Insp.</a> </li>

                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/1")?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Inspected</a> </li>

                                    <li class="nav-item">  <a href="<?=base_url($headData->controller."/index/2")?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Completed</a> </li>

                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/pendingRegrindingIndex") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Pending Regrinding </a> </li>

                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/regrindingChallan/0") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1  active">Regrinding Challan </a> </li>
                                    
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/regrindingInspection/0") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Regrinding Inspection </a> </li>

                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/reRegrindingItem") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 ">Reregrinding </a> </li>
                                </ul>
                            </div>
                            <!-- <div class="col-md-4">
                                <h4 class="card-title text-center">Store Inspection</h4></h4>
                            </div> -->
                            <!--<div class="col-md-4">-->
                            <!--    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addInspection/" data-form_title="Inspection"><i class="fa fa-plus"></i> Inspection</button>-->
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='inspectionTable' class="table table-bordered ssTable" data-url='/getRegrindingChallanDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function() {
initBulkInspectionButton();

$(document).on('click', '.BulkRequest', function() {
    if ($(this).attr('id') == "masterSelect") {
        if ($(this).prop('checked') == true) {
            $(".bulkRegrindingCh").show();
            $("input[name='ref_id[]']").prop('checked', true);
        } else {
            $(".bulkRegrindingCh").hide();
            $("input[name='ref_id[]']").prop('checked', false);
        }
    } else {
        if ($("input[name='ref_id[]']").not(':checked').length != $("input[name='ref_id[]']").length) {
            $(".bulkRegrindingCh").show();
            $("#masterSelect").prop('checked', false);
        } else {
            $(".bulkRegrindingCh").hide();
        }

        if ($("input[name='ref_id[]']:checked").length == $("input[name='ref_id[]']").length) {
            $("#masterSelect").prop('checked', true);
            $(".bulkRegrindingCh").show();
        } else {
            $("#masterSelect").prop('checked', false);
        }
    }
});

$(document).on('click', '.bulkRegrindingCh', function() {
    var ref_id = [];
    $("input[name='ref_id[]']:checked").each(function() {
        ref_id.push(this.value);
    });
    var ids = ref_id.join(",");
    var  button = "both"; if (button == "" || button == null) {;  };
                    var sendData = { id: ids };
                    $.ajax({ 
                        type: "POST",   
                        url: base_url + controller + '/createRegrindingChallan',   
                        data: sendData,
                    }).done(function(response){
                        $("#modal-lg").modal();
                        $('#modal-lg .modal-body').html('');
                        $('#modal-lg .modal-title').html("Create Challan");
                        $('#modal-lg .modal-body').html(response);
                        $("#modal-lg"+" .modal-body form").attr('id','create_challan');
                        //$("#modal-lg"+" .modal-footer .btn-save").html(savebtn_text);
                        $("#modal-lg  .modal-footer .btn-save").attr('onclick',"store('create_challan','saveChallan');");
                        $("#modal-lg  .modal-footer .btn-save-close").attr('onclick',"store('create_challan','saveChallan','save_close');");
                        $("#modal-lg  .modal-footer .btn-close").attr('data-modal_id','create_challan');
                        $("#modal-lg  .modal-footer .btn-close").show();
                        $("#modal-lg  .modal-footer .btn-save").show();
                        initModalSelect();
                        $(".single-select").comboSelect();
                        $('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
                        $("#modal-lg  .scrollable").perfectScrollbar({suppressScrollX: true});
                        initMultiSelect();setPlaceHolder();
                    });

});
});
function initBulkInspectionButton() {
    var bulkRegrindingBtn = '<button class="btn btn-outline-primary bulkRegrindingCh" tabindex="0" aria-controls="inspectionTable" type="button"><span>Bulk Regrinding Challan</span></button>';
    $("#inspectionTable_wrapper .dt-buttons").append(bulkRegrindingBtn);
    $(".bulkRegrindingCh").hide();
}
</script>