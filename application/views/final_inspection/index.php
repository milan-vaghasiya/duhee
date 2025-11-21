<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Final Inspection</h4>
                            </div>
                            <div class="col-md-6">
                                <select name="job_id" id="job_id" class="form-control single-select" style="width:50%;float:right;" >
                                    <option value="">All Job Card</option>
                                    <?php
                                        foreach($jobCardList as $row):
                                            echo '<option value="'.$row->id.'">'.getPrefixNumber($row->job_prefix,$row->job_no).'</option>';
                                        endforeach;
                                    ?>
                                </select>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='finalInspectionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
    $(document).on('change',"#job_id",function(){
        initInspectionTable();
    }); 

    $(document).on('click','.add-item',function(){
        var parameter_id = $("#parameter_id").val();
        var min_qty = $("#min_qty").val();
        var max_qty = $("#max_qty").val();
        var inspector_id = $("#inspector_id").val();
        var ok_qty = $("#ok_qty").val();
        var ud_qty = $("#ud_qty").val();
        var rework_qty = $("#rework_qty").val();
        var mcr_qty = $("#mcr_qty").val();
        var rmr_qty = $("#rmr_qty").val();
        var pending_qty = $("#pending_qty").val();

        $(".parameter_id").html("");
        $(".inspector_id").html("");
        $(".general_error").html("");

        if(parameter_id == "" || inspector_id == "" || ok_qty == "" && ok_qty == "0" && ud_qty == "" && ud_qty == "0" && rework_qty == "" && rework_qty == "0" && mcr_qty == "" && mcr_qty == "0" && rmr_qty == "" && rmr_qty == "0"){
            if(parameter_id == ""){
                $(".parameter_id").html("Parameter is required.");
            }
            if(inspector_id == ""){
                $(".inspector_id").html("Inspector Name is required.");
            }
            if(ok_qty == "" && ok_qty == "0" && ud_qty == "" && ud_qty == "0" && rework_qty == "" && rework_qty == "0" && mcr_qty == "" && mcr_qty == "0" && rmr_qty == "" && rmr_qty == "0"){
                $(".general_error").html("OK/UD/Rework/MC/RM qty is required.");
            }
        }else{

            var totalQty = parseFloat(parseFloat(ok_qty) + parseFloat(ud_qty) + parseFloat(rework_qty) + parseFloat(mcr_qty) + parseFloat(rmr_qty)).toFixed(2);

            if(parseFloat(pending_qty) < parseFloat(totalQty)){
                $(".general_error").html("Invalid Qty.");
            }else{
                
                var postData = {trans_id:"",parameter_id:parameter_id,parameter_name:$("#parameter_idc").val(),min_qty:min_qty,max_qty:max_qty,inspector_id:inspector_id,inspector_name:$("#inspector_idc").val(),ok_qty:ok_qty,ud_qty:ud_qty,rework_qty:rework_qty,mcr_qty:mcr_qty,rmr_qty:rmr_qty,totalQty:totalQty};

                addRaw(postData);
                $("#parameter_id").val("");$("#parameter_id").comboSelect();
                $("#min_qty").val(0);
                $("#max_qty").val(0);
                $("#inspector_id").val("");$("#inspector_id").comboSelect();
                $("#ok_qty").val(0);
                $("#ud_qty").val(0);
                $("#rework_qty").val(0);
                $("#mcr_qty").val(0);
                $("#rmr_qty").val(0);

                var new_pending_qty = parseFloat(parseFloat(pending_qty) - parseFloat(totalQty)).toFixed(2);
                $("#pending_qty").val(new_pending_qty);
                $("#ProductPendingQty").html(new_pending_qty);
            }            
        }
    });
});

function initInspectionTable(){
    var job_id = $("#job_id").val();
    $('.ssTable').dataTable().fnDestroy();
    var tableOptions = {pageLength: 25,'stateSave':false};
    var tableHeaders = {'theads':'','textAlign':textAlign};
    var dataSet = {job_id:job_id};
    ssDatatable($('.ssTable'),tableHeaders,tableOptions,dataSet);
}

function inspection(data){
    var button = data.button;
	var fnEdit = data.fnEdit;if(fnEdit == "" || fnEdit == null){fnEdit="edit";}
	var fnSave = data.fnSave;if(fnSave == "" || fnSave == null){fnSave="save";}
	var postData = {id:data.id,product_name:data.product_name,pending_qty:data.pending_qty,job_card_id:data.job_card_id,product_id:data.product_id,product_code:data.product_code,remark:data.remark};
	$.ajax({ 
		type: "POST",   
		url: base_url + controller + '/' + fnEdit,   
		data: postData,
	}).done(function(response){
		$("#"+data.modal_id).modal();
		$("#"+data.modal_id+' .modal-title').html(data.title);
		$("#"+data.modal_id+' .modal-body').html(response);
		$("#"+data.modal_id+" .modal-body form").attr('id',data.form_id);
		$("#"+data.modal_id+" .modal-footer .btn-save").attr('onclick',"store('"+data.form_id+"','"+fnSave+"');");
		if(button == "close"){
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").hide();
		}else if(button == "save"){
			$("#"+data.modal_id+" .modal-footer .btn-close").hide();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}else{
			$("#"+data.modal_id+" .modal-footer .btn-close").show();
			$("#"+data.modal_id+" .modal-footer .btn-save").show();
		}
		$(".single-select").comboSelect();
		$("#"+data.modal_id+" .scrollable").perfectScrollbar({suppressScrollX: true});
		initMultiSelect();setPlaceHolder();
        //inspectionDataTable();
	});
}

function addRaw(data){
    $('table#inspectionTable tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "inspectionTable";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	//Add index cell
	var countRow = $('#'+tblName+' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);
	cell.attr("style","width:5%;");	
	
	var parameterId = $("<input/>",{type:"hidden",name:"parameter_id[]",value:data.parameter_id});
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:data.trans_id});	
	cell = $(row.insertCell(-1));
	cell.html(data.parameter_name);
	cell.append(parameterId);
	cell.append(transIdInput);
	
	var minQtyInput = $("<input/>",{type:"hidden",name:"min_qty[]",value:data.min_qty});
    cell = $(row.insertCell(-1));
	cell.html(data.min_qty);
	cell.append(minQtyInput);
	
    var maxQtyInput = $("<input/>",{type:"hidden",name:"max_qty[]",value:data.max_qty});
    cell = $(row.insertCell(-1));
	cell.html(data.max_qty);
	cell.append(maxQtyInput);

	var okQtyInput = $("<input/>",{type:"hidden",name:"ok_qty[]",value:data.ok_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.ok_qty);
	cell.append(okQtyInput);
	
	var udQtyInput = $("<input/>",{type:"hidden",name:"ud_qty[]",value:data.ud_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.ud_qty);
	cell.append(udQtyInput);
	
	var reworkQtyInput = $("<input/>",{type:"hidden",name:"rework_qty[]",value:data.rework_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.rework_qty);
	cell.append(reworkQtyInput);

	var mcrQtyInput = $("<input/>",{type:"hidden",name:"mcr_qty[]",value:data.mcr_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.mcr_qty);
	cell.append(mcrQtyInput);

    var rmrQtyInput = $("<input/>",{type:"hidden",name:"rmr_qty[]",value:data.rmr_qty});
	cell = $(row.insertCell(-1));
	cell.html(data.rmr_qty);
	cell.append(rmrQtyInput);

    var inspectorIdInput = $("<input/>",{type:"hidden",name:"inspector_id[]",value:data.inspector_id});
	cell = $(row.insertCell(-1));
	cell.html(data.inspector_name);
	cell.append(inspectorIdInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this,'"+data.totalQty+"');");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
}

function Remove(button,qty) {
	//Determine the reference of the Row using the Button.
	var row = $(button).closest("TR");
	var table = $("#inspectionTable")[0];
	table.deleteRow(row[0].rowIndex);
	$('#inspectionTable tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#inspectionTable tbody tr:last').index() + 1;
    if(countTR == 0){
	    $("#inspectionData").html('<tr id="noData"><td colspan="11" align="center">No data available in table</td></tr>');
    }

    var pending_qty = $("#pending_qty").val();
    var new_pending_qty = parseFloat(parseFloat(pending_qty) + parseFloat(qty)).toFixed(2);
    $("#pending_qty").val(new_pending_qty);
    $("#ProductPendingQty").html(new_pending_qty);
};

function acceptInspection(id,name='Inspection'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to accept this '+name+'?',
		type: 'green',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/acceptInspection',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data)
						{
							if(data.status==0)
							{
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
							else
							{
								initInspectionTable();
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){

				}
            }
		}
	});
}

function inspectionSave(formId,fnsave){
    setPlaceHolder();
	if(fnsave == "" || fnsave == null){fnsave="save";}
	var form = $('#'+formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data:fd,
		type: "POST",
		processData:false,
		contentType:false,
		dataType:"json",
	}).done(function(data){
		if(data.status===0){
			$(".error").html("");
			$.each( data.message, function( key, value ) {$("."+key).html(value);});
		}else if(data.status==1){
			initInspectionTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}else{
			initInspectionTable(); $('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}
</script>