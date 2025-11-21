<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-10">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/index/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 "> Inward</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/pendingPackingIndex/") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 "> Pending Packing</a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/packingIndex/0") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Inprocess </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/packingIndex/1") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Completed </a> </li>
                                    <li class="nav-item"> <a href="<?= base_url($headData->controller . "/firstBoxPacking") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1 active"> First/Loose Box </a> </li>
									<li class="nav-item"> <a href="<?= base_url($headData->controller . "/materialshortage") ?>" class="btn waves-effect waves-light btn-outline-info  permission-write mr-1"> Material Shortage </a> </li>

								</ul>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNewFB permission-write" data-button="both" data-modal_id="modal-xl" data-function="addFirstBox" data-form_title="Add First Box/Loos Box"><i class="fa fa-plus"></i> First Box/Loos Box</button>

                            </div>
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingTable' class="table table-bordered ssTable" data-url='/getFBDTRows'></table>
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
	$(document).on('click',".addNewFB",function(){
		var functionName = $(this).data("function");
		var modalId = $(this).data('modal_id');
		var button = $(this).data('button');
		var title = $(this).data('form_title');
		var formId = functionName.split('/')[0];
		var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}
		$.ajax({ 
			type: "GET",   
			url: base_url + controller + '/' + functionName,   
			data: {}
		}).done(function(response){
			$("#"+modalId).modal({show:true});
			$("#"+modalId+' .modal-title').html(title);
			$("#"+modalId+' .modal-body').html("");
			$("#"+modalId+' .modal-body').html(response);
			$("#"+modalId+" .modal-body form").attr('id',formId);
			$("#"+modalId+" .modal-footer .btn-save").attr('onclick',"storeFB('"+formId+"','"+fnsave+"');");
				
			if(button == "close"){
				$("#"+modalId+" .modal-footer .btn-close").show();
				$("#"+modalId+" .modal-footer .btn-save").hide();
			}else if(button == "save"){
				$("#"+modalId+" .modal-footer .btn-close").hide();
				$("#"+modalId+" .modal-footer .btn-save").show();
			}else{
				$("#"+modalId+" .modal-footer .btn-close").show();
				$("#"+modalId+" .modal-footer .btn-save").show();
			}
			initModalSelect();
			$(".single-select").comboSelect();
			$('.model-select2').select2({ dropdownParent: $('.model-select2').parent() });
			$("#processDiv").hide();
			$("#"+modalId+" .scrollable").perfectScrollbar({suppressScrollX: true});
			setTimeout(function(){ initMultiSelect();setPlaceHolder(); }, 5);
		});
	});	

	$(document).on('keyup change',".batchQty",function(){		
		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(this).val("");
		}
	});

	$(document).on('keyup change',".fgBatchQty",function(){		
		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(this).val("");
		}
	});

});
function trashFirstBox(id,name='Record'){
	
	var send_data = { id:id };
	$.confirm({
		title: 'Confirm!',
		content: 'Are you sure want to delete this '+name+'?',
		type: 'red',
		buttons: {   
			ok: {
				text: "ok!",
				btnClass: 'btn waves-effect waves-light btn-outline-success',
				keys: ['enter'],
				action: function(){
					$.ajax({
						url: base_url + controller + '/deleteFb',
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


function storeFB(formId,fnsave){

	var batchQtyArr = $("input[name='batch_qty[]']").map(function(){return $(this).val();}).get();
	var batchQtySum = 0;
	$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
	var stockQtyArr = $("input[name='stock_qty[]']").map(function(){return $(this).val();}).get();
	var stockQtySum = 0;
	$.each(stockQtyArr,function(){stockQtySum += parseFloat(this) || 0;});

	var totalQty = $("#max_qty_per_box").val();
	pendingQty = totalQty - batchQtySum;
	stkQty = stockQtySum-batchQtySum;
	if(pendingQty>0 && (stkQty >= pendingQty)){
		$.confirm({
		title: 'Confirm!',
		content: 'You still have stock. Are you sure want to save this First box ?',
		type: 'red',
		buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						store(formId,fnsave);
					}
				},
				cancel: {
						btnClass: 'btn waves-effect waves-light btn-outline-secondary',
						action: function(){

					}
				}
			}
		});
	}else{
		store(formId,fnsave);
		// alert("OK");
	}
}

</script>