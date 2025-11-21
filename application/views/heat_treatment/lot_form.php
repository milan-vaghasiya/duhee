<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>
                            <u>Furnace Lot</u>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form id="furnace_form">
                                <div class="row">
                                    <input type="hidden" name="id" value="<?= (!empty($dataRow->id) && empty($revision)) ? $dataRow->id : '' ?>">
                                    <div class="col-md-3 form-group">
                                        <label for="trans_date">Date</label>
                                        <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?= !empty($dataRow->trans_date) ? $dataRow->trans_date : date("Y-m-d") ?>">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="furnace_id">Furnace</label>
                                        <select id="furnace_id" name="furnace_id"  class="form-control single-select">
                                            <option value="">Select Furnace</option>
                                            <?php
                                            if (!empty($furnaceList)) {
                                                foreach ($furnaceList as $row) {
                                                    $selected = (!empty($dataRow->furnace_id) && $dataRow->furnace_id == $row->id) ? 'selected' : '';
                                                    
                                                    $disabled='';
                                                    if(!empty($dataRow->id)){
                                                        $disabled = (!empty($dataRow->furnace_id) && $dataRow->furnace_id != $row->id) ? 'disabled' : '';
                                                    }

                                            ?>
                                            
                                            <option value="<?= $row->id ?>" data-code="<?= $row->furnace_no ?>" <?= $selected ?> <?=$disabled?>><?=$row->furnace_no?></option>
                                            
                                            <?php }
                                            } ?>
                                        </select>
                                        <input type="hidden" name="furnace_no" id="furnace_no" value="<?= !empty($dataRow->furnace_no) ? $dataRow->furnace_no : '' ?>">
                                        <div class="error furnace_id"></div>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label for="trans_number">SQF Batch No.</label>
                                        <input type="text" class="form-control" id="trans_number" name="trans_number"  value="<?=(!empty($dataRow->trans_number)?$dataRow->trans_number:'')?>">

                                        <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no)?$dataRow->trans_no:$nextTransNo)?>" >

                                        <input type="hidden" name="month" id="month" value="<?=(!empty($dataRow->trans_date)?n2m(date("m",strtotime($dataRow->trans_date))):n2m(date('m')))?>" >

                                        <input type="hidden" name="year" id="year" value="<?=(!empty($dataRow->trans_date)?(date("y",strtotime($dataRow->trans_date))):(date('y')))?>" >
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <button type="button" class="btn btn-outline-success btn-save float-right" onclick="addRow()"><i class="fa fa-plus"></i> Add</button>
                                    </div>
                                    <div class="error general_error"></div>
                                    <div class="table-responsive" style="height:50vh;overflow-y:scroll;">
                                        <table id="furnacetable" class="table table-bordered " style="font-size: 11px !important;">
                                            <thead class="thead-info">
                                                <tr>
                                                    <th style="width:5px;">#</th>
                                                    <th>Product</th>
                                                    <th>Job Number</th>
                                                    <th>Qty</th>
                                                    <th>W/P</th>
                                                    <th>Total Kg</th>
                                                    <th>Remark</th>
                                                    <th class="text-center" style="width:13px;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="furnaceTbody">

                                                <tr id="noData">
                                                    <td colspan="8" align="center">No data available in table</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <button type="button" class="btn waves-effect waves-light btn-outline-success float-right save-form" onclick="saveLot('furnace_form','saveLot');"><i class="fa fa-check"></i> Save</button>
                            <a href="<?= base_url($headData->controller) ?>" class="btn waves-effect waves-light btn-outline-secondary float-right save-form" style="margin-right:10px;"><i class="fa fa-times"></i> Cancel</a>
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

	$(document).on('change','#furnace_id',function(){
		var code = $(this).find(":selected").data('code');
		$("#furnace_no").val(code);
		var trans_no = "";
		var month = $("#month").val();
		var year = $("#year").val();
		
		$.ajax({
			url : base_url + controller+'/getTransNo',
			type: 'post',
			data:{furnace_id:$(this).val()},
			dataType:'json',
			success:function(data){					
				if (data.trans_no >= 1 && data.trans_no <= 9) {
					trans_no = (data.trans_no < 10) ? '0' + data.trans_no : data.trans_no.toString();
				}
				
				if(code !== undefined){
					$("#trans_number").val(code+trans_no+month+year);
				}else{
					$("#trans_number").val("");
				}
				$("#trans_no").val(data.trans_no);
			}
		});
	});
	
	$(document).on("change","#trans_date",function(e){
		e.stopImmediatePropagation();e.preventDefault();
		getMonthLetter($(this).val());
	});
	
	$(document).on('change', '.getBatch', function() {
		var row_id = $(this).find(":selected").data('row_id');
		var wt_pcs = $(this).find(":selected").data('wt_pcs');
		var item_id = $(this).val();
		$("#wt_pcs"+row_id).val(wt_pcs);
		$.ajax({
			url : base_url + controller+'/getBatchNo',
			type: 'post',
			data:{item_id:item_id},
			dataType:'json',
			success:function(data){
				$("#batch_no"+row_id).html("");
				$("#batch_no"+row_id).html(data.options);
				$("#batch_no"+row_id).comboSelect();
			}
		});
	});

	$(document).on('keyup', '.calKg', function() {   
		var row_id = $(this).data('row_id');
		var stock_qty = $("#batch_no"+row_id).find(":selected").data('stock_qty');
		var batch_wt = $("#item_id"+row_id).find(":selected").data('batch_wt');
		var qty = $(this).val();

		if(parseFloat(qty) > parseFloat(stock_qty)){
			$(".qty"+row_id).html("Invalid Qty");
		}else{

			var wt_pcs = $("#wt_pcs"+row_id).val() || 0;
			var total_kg = parseFloat(qty)*parseFloat(wt_pcs);
			$("#total_kg"+row_id).val(total_kg);
			if(parseFloat(total_kg) > batch_wt){$("#total_kg"+row_id).css('background-color','#a90000');$("#total_kg"+row_id).css('color','#FFFFFF');}
			else{$("#total_kg"+row_id).css('background-color','#f6f6f6');$("#total_kg"+row_id).css('color','#555');}
		}
	   
	});
});

function getMonthLetter(month){
	$.ajax({
		url: base_url + controller + '/getMonthLetter',
		type: 'post',
		data:{month:month},
		dataType:'json',
	}).done(function(response) {
		if (response != "") {
			$("#month").val(response);
			$("#furnace_id").trigger("change");
		}else{
			$("#month").val("");
		}
	});
}

function saveLot(formId, fnsave) {
	// var fd = $('#'+formId).serialize();

	if (fnsave == "" || fnsave == null) {
		fnsave = "save";
	}
	var form = $('#' + formId)[0];
	var fd = new FormData(form);
	$.ajax({
		url: base_url + controller + '/' + fnsave,
		data: fd,
		type: "POST",
		processData: false,
		contentType: false,
		dataType: "json",
	}).done(function(data) {
		if (data.status === 0) {
			$(".error").html("");
			$.each(data.message, function(key, value) {
				$("." + key).html(value);
			});
		} else if (data.status == 1) {
			toastr.success(data.message, 'Success', {
				"showMethod": "slideDown",
				"hideMethod": "slideUp",
				"closeButton": true,
				positionClass: 'toastr toast-bottom-center',
				containerId: 'toast-bottom-center',
				"progressBar": true
			});
			window.location = data.url;

		} else {
			initTable(0);
			$('#' + formId)[0].reset();
			$(".modal").modal('hide');
			toastr.error(data.message, 'Error', {
				"showMethod": "slideDown",
				"hideMethod": "slideUp",
				"closeButton": true,
				positionClass: 'toastr toast-bottom-center',
				containerId: 'toast-bottom-center',
				"progressBar": true
			});
		}

	});
}

function addRow(data = {}) {
	$('table#furnacetable tr#noData').remove()
	//Get the reference of the Table's TBODY element.
	var tblName = "furnacetable";

	var tBody = $("#" + tblName + " > TBODY")[0];

	//Add Row.
	row = tBody.insertRow(-1);

	//Add index cell
	var countRow = $('#' + tblName + ' tbody tr:last').index() + 1;
	var cell = $(row.insertCell(-1));
	cell.html(countRow);

   
	var idIP = $("<input/>", {
		type: "hidden",
		name: "trans_id[]",
		value: data.id
	});

	var itemIdIp = $("<select/>", {
		type: "text",
		name: "item_id["+(countRow-1)+"]",
		id:'item_id'+countRow,
		class: "form-control single-select getBatch"
	});
	var itemArray = <?php echo json_encode($itemList); ?>;
	var options = '<option value=""  data-wt_pcs="">Select Item</option>';
	itemIdIp.append(options);

	$.each(itemArray, function(key,itemRow) {
		var selectedOpt = (data.item_id == itemRow.id) ? 'selected' : '';
		var options = '<option value="' + itemRow.id + '" ' + selectedOpt + ' data-batch_wt="'+itemRow.batch_wt+'" data-wt_pcs="'+itemRow.wt_pcs+'" data-row_id="'+countRow+'">['+itemRow.item_code+'] ' + itemRow.item_name + '</option>';
		itemIdIp.append(options);
	});
	
	cell = $(row.insertCell(-1));
	cell.html(itemIdIp);
	cell.append(idIP);
	cell.append('<div class="error item_id'+countRow+'"></div>');

	var batchIp = $("<select/>", {
		type: "text",
		name: "batch_no["+(countRow-1)+"]",
		id:'batch_no'+countRow,
		class: "form-control single-select"
	});
	var options = '<option value=""  data-wt_pcs="">Select Batch No</option>';
	batchIp.append(options);
	if(data.batchList){
		var batchArray = JSON.parse(data.batchList);
		$.each(batchArray, function(key,batch) {
			
			
			if(batch.batch_no == data.batch_no || batch.qty > 0){
				var stock_qty = batch.qty;
				if(batch.batch_no == data.batch_no){
					stock_qty = batch.qty+data.qty;
				}
				var selectedOpt = (data.batch_no == batch.batch_no) ? 'selected' : '';
				var options = '<option value="' + batch.batch_no + '" ' + selectedOpt + ' data-stock_qty="'+stock_qty+'">' + batch.batch_no + '</option>';
				batchIp.append(options);
			}
			
			
		});
	}
	
	cell = $(row.insertCell(-1));
	cell.html(batchIp);
	cell.append('<div class="error batch_no'+countRow+'"></div>');
	
	var qtyIP = '<input type="text" name="qty[]" id="qty'+countRow+'" value="'+(data.qty||'')+'" data-row_id="'+countRow+'" class="form-control floatOnly calKg">';
	cell = $(row.insertCell(-1));
	cell.html(qtyIP);
	cell.append('<div class="error qty'+countRow+'"></div>');

	var wtPcsIp = $("<input/>", {
		type: "text",
		name: "wt_pcs[]",
		value: data.wt_pcs,
		id:'wt_pcs'+countRow,
		readonly:true,
		data:"row_id="+countRow,
		class: "form-control"
	});
	cell = $(row.insertCell(-1));
	cell.html(wtPcsIp);

	var totalKgIp = $("<input/>", {
		type: "text",
		name: "total_kg[]",
		value: data.total_kg,
		id:'total_kg'+countRow,
		readonly:true,
		class: "form-control"
	});
	cell = $(row.insertCell(-1));
	cell.html(totalKgIp);

	var remarkIp = $("<input/>", {
		type: "text",
		name: "remark[]",
		value: data.remark,
		id:'remark'+countRow,
		class: "form-control"
	});
	cell = $(row.insertCell(-1));
	cell.html(remarkIp);

	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button sy><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");
	cell.append(btnRemove);

	// cell.attr("class", "text-center");
	// cell.attr("style", "width:5%;");

   
	$(".single-select").comboSelect();

	initMultiSelect();
}

function Remove(button) {
	//Determine the reference of the Row using the Button.
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Remove this Record? <br> All related records will be removed and will not be recovered',
		type: 'red',
		buttons: {
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function() {
					var row = $(button).closest("TR");
					var table = $("#furnacetable")[0];
					table.deleteRow(row[0].rowIndex);
					$('#furnacetable tbody tr td:nth-child(1)').each(function(idx, ele) {
						ele.textContent = idx + 1;
					});

					$('#furnacetable tbody tr td:nth-child(2) select').each(function(idx, ele) {
						let newIdx = parseFloat(idx) + 1;

						$(this).attr('id', 'item_id' + newIdx);
						$('#item_id' + newIdx+" option").each(function(){
							$(this).attr('data-row_id', newIdx);
						});
					   
					});
					$('#furnacetable tbody tr td:nth-child(2)  .error').each(function(idx, ele) {
						let newIdx = parseFloat(idx) + 1;
						$(this).attr('class', 'error item_id' + newIdx);
					});

					$('#furnacetable tbody tr td:nth-child(3)  select').each(function(idx, ele) {

						let newIdx = parseFloat(idx) + 1;
						$(this).attr('id', 'batch_no' + newIdx);
						$(this).attr('data-row_id', newIdx);
						$('#batch_no' + newIdx+" option").each(function(){
							$(this).attr('data-row_id', newIdx);
						});
					});
					$('#furnacetable tbody tr td:nth-child(3)  .error').each(function(idx, ele) {
						let newIdx = parseFloat(idx) + 1;
						$(this).attr('class', 'error batch_no' + newIdx);
					});
					$('#furnacetable tbody tr td:nth-child(4)  input').each(function(idx, ele) {
						let newIdx = parseFloat(idx) + 1;
						$(this).attr('id', 'qty' + newIdx);
						$(this).attr('data-row_id', newIdx);
					});

					$('#furnacetable tbody tr td:nth-child(4)  .error').each(function(idx, ele) {
						let newIdx = parseFloat(idx) + 1;
						$(this).attr('class', 'error qty' + newIdx);
					});

					$('#furnacetable tbody tr td:nth-child(5)  input').each(function(idx, ele) {
						let newIdx = parseFloat(idx) + 1;
						$(this).attr('id', 'wt_pcs' + newIdx);
						$(this).attr('data-row_id', newIdx);
					});

					var countTR = $('#furnacetable tbody tr:last').index() + 1;
					if (countTR == 0) {
						$("#furnaceTbody").html('<tr id="noData"><td colspan="24" align="center">No data available in table</td></tr>');
					}
					$(".single-select").comboSelect();
				}
			},
			cancel: {
				btnClass: 'btn waves-effect waves-light btn-outline-secondary',
				action: function() {

				}
			}
		}
	});
};

</script>

<?php
if (!empty($transData)) {
    foreach ($transData as $row) {
        echo "<script>addRow(" . json_encode($row) . ");</script>";
    }
}
?>