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
                                    <li class="nav-item"> <button onclick="statusTab('inspectionTable',0,0);" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 <?=empty($status)?'active':''?>" data-toggle="tab" aria-expanded="false">Pending Insp.</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('inspectionTable',1,0);" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 <?=($status == 1)?'active':''?>" data-toggle="tab" aria-expanded="false">Inspected</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('inspectionTable',2,0);" class="nav-link btn waves-effect waves-light btn-outline-info mr-1 <?=($status == 2)?'active':''?>" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <!--<li class="nav-item"> <a href="<?=base_url($headData->controller."/pendingRegrindingIndex")?>"  class="nav-link btn waves-effect waves-light btn-outline-info mr-1" >Pending Regrinding </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/regrindingChallan") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Regrinding Challan </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/regrindingInspection") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Regrinding Inspection </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/reRegrindingItem") ?>" class="nav-link btn waves-effect waves-light btn-outline-info mr-1">Reregrinding </a> </li>-->

                                </ul>
                            </div>
                            <!--<div class="col-md-4">-->
                            <!--    <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="addInspection/" data-form_title="Inspection"><i class="fa fa-plus"></i> Inspection</button>-->
                            <!--</div>-->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='inspectionTable' class="table table-bordered ssTable" data-url='/getDTRows/<?=$status?>'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="convertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Convert to Other</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="convert_ref_id" value="">
                <div class="col-md-12 form-group">
                    <label for="other_item_id">Item</label>
                    <select id="other_item_id" class="form-control large-select2 req" data-item_type="3" data-category_id="" data-family_id="" autocomplete="off" data-default_id="<?= (!empty($dataRow->req_item_id)) ? $dataRow->req_item_id : "" ?>" data-default_text="<?= (!empty($dataRow->full_name)) ? $dataRow->full_name : "" ?>" data-url="items/getDynamicItemList">
                        <option value="">Select Item</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-convert" data-dismiss="modal"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="regrindingReasonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Regrinding Reason</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="regrinding_row_id" value="">
                <div class="col-md-12 form-group">
                    <label for="regrinding_reason">Reason</label>
                    <select id="regrinding_reason" class="form-control single-select req" >
                        <option value="">Select Reason</option>
                        <?php
                        if(!empty($reasonList)){
                            foreach($reasonList as $row){
                                ?>
                                <option value="<?=$row->id?>"><?=$row->remark?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                <button type="button" class="btn waves-effect waves-light btn-outline-success btn-add-reason" data-dismiss="modal"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click','.convertItem',function(){
        $('#convert_ref_id').val($(this).data('btn_id'));
        $("#convertModal").modal(); 		
	});

    $(document).on('click','.btn-convert',function(){
        var item_id = $("#other_item_id").val(); 
        var convert_ref_id = $("#convert_ref_id").val();
        $('#convert_item_id_'+convert_ref_id).val(item_id);
        $("#convertModal").modal(); 
    });

    $(document).on('click','.regrindingReason',function(){
        $('#regrinding_row_id').val($(this).data('btn_id'));
        $("#regrindingReasonModal").modal(); 		
	});

    $(document).on('click','.btn-add-reason',function(){
        var regrinding_reason = $("#regrinding_reason").val(); 
        var regrinding_row_id = $("#regrinding_row_id").val();
        $('#regrinding_reason_'+regrinding_row_id).val(regrinding_reason);
        $("#regrindingReasonModal").modal(); 
    });


    $(document).on('click','.saveInspection',function(){
        var btn_id = $(this).data('btn_id');
        var return_qty = $("#return_qty_" + btn_id).val(); 
        var used_qty = $("#used_qty_" + btn_id).val(); 
        var fresh_qty = $("#fresh_qty_" + btn_id).val();
        var scrap_qty = $("#scrap_qty_" + btn_id).val(); 
        var regranding_qty = $("#regranding_qty_" + btn_id).val();
        var regrinding_reason = $("#regrinding_reason_" + btn_id).val();
        var convert_qty = $("#convert_qty_" + btn_id).val(); 
        var convert_item_id = $("#convert_item_id_" + btn_id).val();
        var broken_qty = $("#broken_qty_" + btn_id).val(); 
        var miss_qty = $("#miss_qty_" + btn_id).val();

        var postData = { 
            id : btn_id, 
            return_qty : return_qty,
            used_qty : used_qty, 
            fresh_qty : fresh_qty, 
            scrap_qty : scrap_qty, 
            regranding_qty : regranding_qty, 
            regrinding_reason : regrinding_reason, 
            convert_qty : convert_qty, 
            convert_item_id : convert_item_id, 
            broken_qty : broken_qty, 
            miss_qty : miss_qty      
        };

        $.ajax({
			url: base_url + controller + '/saveInspection',
			data:postData,
			type: "POST",
			dataType:"json",
			success:function(data)
			{
				if(data.status===0)
				{
					$(".error").html("");
					$.each( data.message, function( key, value ) {
						$("."+key).html(value);
					});
				}
				else if(data.status==1)
				{
                    initTable(0);
			        toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		
                    $("#used_qty_" + btn_id).val(); 
                    $("#fresh_qty_" + btn_id).val();
                    $("#scrap_qty_" + btn_id).val(); 
                    $("#regranding_qty_" + btn_id).val();
                    $("#convert_qty_" + btn_id).val(); 
                    $("#convert_item_id_" + btn_id).val();
                    $("#broken_qty_" + btn_id).val(); 
                    $("#miss_qty_" + btn_id).val();
				}
				else
				{
					initTable(0);
			        toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		        }
			}
		});
    });
});

// function openViewInspectionTrans(id, item_name) {
//     var button = 'close';
//     $.ajax({
//         type: "POST",
//         url: base_url + controller + '/inspectionView',
//         data: {
//             id: id
//         },
//     }).done(function(response) {
//         $("#modal-lg").modal();
//         $("#modal-lg .modal-title").html(item_name);
//         $("#modal-lg .modal-body").html(response);
//         $("#" + 'modal-lg' + " .modal-body form").attr('id', 'transView');
//         $("#" + 'modal-lg' + " .modal-footer .btn-close").show();
//         $("#" + 'modal-lg' + " .modal-footer .btn-save-insp").show();
// 		$("#modal-lg .modal-footer .btn-save-insp").attr('onclick',"saveInspLocation('saveInspLocation');");
//         initModalSelect();
//         $(".single-select").comboSelect();
//         $("#" + 'modal-lg' + " .scrollable").perfectScrollbar({
//             suppressScrollX: true
//         });
//         initMultiSelect();
//         setPlaceHolder();
//     });
// }
</script>