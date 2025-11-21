<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
	<div class="container-fluid bg-container">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
						<div class="row">
							<div class="col-md-4">
                                <!-- <h4 class="card-title"></h4> -->
                               
									<a href="<?= base_url($headData->controller . "/index") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write">Pending</a>
									<a href="<?= base_url($headData->controller . "/indexCompleted") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write">Completed</a>
									<a href="<?= base_url($headData->controller . "/indexReturn") ?>" class="btn waves-effect waves-light btn-outline-primary permission-write active">Return</a>
								
                            </div>
							<div class="col-md-4">
								<h4 class="card-title text-center">Job Work (Vendor)</h4>
							</div>
							<div class="col-md-4">
								<button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="jobWorkOut" data-form_title="Jobwork"><i class="fa fa-plus"></i> Add Jobwork</button>
							</div>
						</div>
					</div>
					<div class="card-body">
						<input type="hidden" id="process_id" value="">
						<div class="table-responsive">
							<table id='jobWorkReturnTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0]' data-url='/getDTReturnRows'></table>
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
	$('.model-select2').select2({
		dropdownParent: $('.model-select2').parent()
	});

	$(document).on('change', "#job_order_trans_id", function() {
		var item_id = $(this).val();
		$.ajax({
			url: base_url + controller + '/getLocationListBasedOnItem',
			data: {
				item_id: item_id
			},
			type: "POST",
			dataType: "json",
		}).done(function(response) {
			console.log(response.options);
			$("#location_id").html();
			$("#location_id").html(response.options);
			$("#location_id").comboSelect();
		});
	});

	$(document).on("change","#location_id",function(){
		var itemId = $("#job_order_trans_id").val();
        var location_id = $(this).val();
		$(".location_id").html("");
		$(".job_order_trans_id").html("");
		
		if(itemId == "" || location_id == ""){
			if(itemId == ""){
				$(".job_order_trans_id").html("Item name is required.");
			}
			if(location_id == ""){
				$(".location_id").html("Location is required.");
			}
		}else{
			$.ajax({
				url:base_url + controller + '/getBatchNo',
				type:'post',
				data:{item_id:itemId,location_id:location_id},
				dataType:'json',
				success:function(data){
					$("#batch_no").html("");
					$("#batch_no").html(data.options);
					$("#batch_no").comboSelect();
				}
			});
		}
	});

	$(document).on('change', "#job_order_trans_id", function() {
		var trans_id = $(this).val();
		if(trans_id){
			var unit_name = $('#job_order_trans_id :selected').data('unit_name');			
			$("#qtyLabel").html('(' + unit_name + ')');			
		}else{
			$("#qtyLabel").html('');
			
		}
	});

	$(document).on('change keyup','#jobwork_process_id',function(){
		$("#process_name").val($('#jobwork_process_idc').val()); 
	});

	$(document).on('change keyup','#vendor_id',function(){
		$('#vendor_name').val($('#vendor_idc').val());
		
		var vendor_id = $(this).val();
		if(vendor_id){
			$.ajax({
				url:base_url + controller + '/getJobOrderByVendor',
				type:'post',
				data:{vendor_id:vendor_id},
				dataType:'json',
				success:function(data){
					$("#job_order_trans_id").html("");
					$("#job_order_trans_id").html(data.itemOptions);
					$("#job_order_trans_id").comboSelect();
				}
			});
		} else { 
			$("#job_order_trans_id").html('<option value="">Select Item</option>');
			$("#job_order_trans_id").comboSelect();
		}
	});

	$(document).on('click', '.addRow', function() {
		var job_order_trans_id = $("#job_order_trans_id").val();
		var qty = $("#qty").val();
		var jobwork_process_id = $("#jobwork_process_id").val();
		var process_name = $('#jobwork_process_idc').val()
		var trans_remark = $("#trans_remark").val();
		var location_id = $("#location_id").val();
		var batch_no = $("#batch_no").val();
		
		var IsValid = 1;
		if (item_id == "") { $(".item_id").html("Item is required."); IsValid=0;}
		if (jobwork_process_id == "") { $(".jobwork_process_id").html("Process is required."); IsValid=0;}
		if (qty == "" || qty == "0" || qty == "0.000") { $(".qty").html("Qty. is required."); IsValid=0;}
		
		if(IsValid){
			var item_id=$('#job_order_trans_id :selected').data('item_id');			
			var item_name=$('#job_order_trans_id :selected').data('item_name');		
			var price=$('#job_order_trans_id :selected').data('price');
			var post = {
				id: "",
				qty: qty,
				job_order_trans_id:job_order_trans_id,
				item_id: item_id,
				location_id: location_id,
				batch_no: batch_no,
				jobwork_process_id: jobwork_process_id,
				item_name:item_name,
				process_name: process_name,
				item_name:item_name,
				price: price,
				trans_remark: trans_remark
			};

			addRow(post);
			$("#job_order_trans_id").val("");
			$("#job_order_trans_id").comboSelect();
			$("#jobwork_process_id").val("");
			$("#jobwork_process_id").comboSelect();
			$("#location_id").val("");
			$("#location_id").comboSelect();
			$("#batch_no").val("");
			$("#batch_no").comboSelect();
			$("#qty").val("");
			$("#trans_remark").val("");
			setPlaceHolder();
		}
	});
});

function addRow(data) {
	$('table#jobworkItems tr#noData').remove();
	//Get the reference of the Table's TBODY element.
	var tblName = "jobworkItems";
	
	var tBody = $("#"+tblName+" > TBODY")[0];
	
	//Add Row.
	row = tBody.insertRow(-1);
	
	var transIdInput = $("<input/>",{type:"hidden",name:"trans_id[]",value:''});
	var itemIdInput = $("<input/>",{type:"hidden",name:"item_id[]",value:data.item_id});
	var locationIdInput = $("<input/>",{type:"hidden",name:"location_id[]",value:data.location_id});
	var batchNoInput = $("<input/>",{type:"hidden",name:"batch_no[]",value:data.batch_no});
	var jobtransIdInput = $("<input/>",{type:"hidden",name:"job_order_trans_id[]",value:data.job_order_trans_id});
	var remarkInput = $("<input/>",{type:"hidden",name:"trans_remark[]",value:data.trans_remark});
	var priceInput = $("<input/>",{type:"hidden",name:"price[]",value:data.price});
	cell = $(row.insertCell(-1));
	cell.html(data.item_name);
	cell.append(itemIdInput);
	cell.append(locationIdInput);
	cell.append(batchNoInput);
	cell.append(jobtransIdInput);
	cell.append(remarkInput);
	cell.append(priceInput);
	cell.append(transIdInput);
	
	var processInput = $("<input/>",{type:"hidden",name:"jobwork_process_id[]",value:data.jobwork_process_id});
	cell = $(row.insertCell(-1));
	cell.html(data.process_name);
	cell.append(processInput);
	
    var qtyInput = $("<input/>",{type:"hidden",name:"qty[]",value:data.qty});
	cell = $(row.insertCell(-1));
	cell.html(data.qty);
	cell.append(qtyInput);
	
	//Add Button cell.
	cell = $(row.insertCell(-1));
	var btnRemove = $('<button><i class="ti-trash"></i></button>');
	btnRemove.attr("type", "button");
	btnRemove.attr("onclick", "Remove(this);");
    btnRemove.attr("style","margin-left:4px;");
	btnRemove.attr("class", "btn btn-outline-danger waves-effect waves-light");

	cell.append(btnRemove);
	cell.attr("class","text-center");
	cell.attr("style","width:10%;");
};

function Remove(button) {
	var row = $(button).closest("TR");
	var table = $("#jobworkItems")[0];
	table.deleteRow(row[0].rowIndex);
	$('#jobworkItems tbody tr td:nth-child(1)').each(function(idx, ele) {
        ele.textContent = idx + 1;
    });
	var countTR = $('#jobworkItems tbody tr:last').index() + 1;
	if(countTR == 0){
		$("#tempItem").html('<tr id="noData"><td colspan="4" align="center">No data available in table</td></tr>');
	}	
};
function approveReturn(id,name='Record'){
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to Approve '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/approveJobWorkReturn',
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
								initTable(); initMultiSelect();
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
</script>