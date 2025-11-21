<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"> <button onclick="statusTab('purchaseRequestTable',0);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('purchaseRequestTable',1);" class=" btn waves-effect waves-light btn-outline-success" style="outline:0px" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                    <li class="nav-item"> <button onclick="statusTab('purchaseRequestTable',3);" class=" btn waves-effect waves-light btn-outline-primary" style="outline:0px" data-toggle="tab" aria-expanded="false">Reject</button> </li>
                                </ul>
                            </div>   
                            <div class="col-md-4">
                                <h4 class="card-title text-center">Purchase Indent</h4>
                            </div>
							<div class="col-md-4">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-xl" data-function="generateDirectIndent" data-fnsave="savePurchaseIndent" data-form_title="Purchase Indent" ><i class="fa fa-plus"></i> Create Purchase Indent</button>
                            </div> 
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseRequestTable' class="table table-bordered ssTable ssTable-cf tfs-12" data-ninput='[0,1,2,-1]'  data-url='/getDTIndentRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content animated slideDown">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLabel1">Create Purchase Order</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="">
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Part Name</th>
                                        <th class="text-center">Qty.</th>
                                    </tr>
                                </thead>
                                <tbody id="orderData">
                                    <tr>
                                        <td class="text-center" colspan="3">No Data Found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn waves-effect waves-light btn-outline-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create Challan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click',".approvePreq",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Purchase Request?',
			type: 'green',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/approvePreq',
							data: {id:id,val:val,msg:msg},
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
								    initTable(); 
									toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
									//window.location.reload();
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
	});
	
	$(document).on('click',".closePreq",function(){
		var id = $(this).data('id');
		var val = $(this).data('val');
        var msg= $(this).data('msg');
		$.confirm({
			title: 'Confirm!',
			content: 'Are you sure want to '+ msg +' this Purchase Request?',
			type: 'red',
			buttons: {   
				ok: {
					text: "ok!",
					btnClass: 'btn waves-effect waves-light btn-outline-success',
					keys: ['enter'],
					action: function(){
						$.ajax({
							url: base_url + controller + '/closePreq',
							data: {id:id,val:val,msg:msg},
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
                                    initTable(); 
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
	});
	
	/*  Created By : Avruti @7-12-2021 04:00 PM
        update by : 
        note : 
    */  
	$(document).on('click','.createPurchaseOrder',function(){
		$.ajax({
			url : base_url + controller + '/getPurchaseOrder',
			type: 'post',
			data:{},
			dataType:'json',
			success:function(data){
				$("#orderModal").modal();
				$("#exampleModalLabel1").html('Create Purchase Order');
				$("#party_so").attr('action',base_url + 'purchaseOrder/createPurchaseOrder');
				$("#btn-create").html('<i class="fa fa-check"></i> Create Purchase Order');
				$("#orderData").html("");
				$("#orderData").html(data.htmlData);
			}
		});
	});

});

function statusTab(tableId, status) {
		$("#" + tableId).attr("data-url", '/getDTIndentRows/' + status);
		ssTable.state.clear();
		initTable();
	}

	function request(data) {
		var button = "";
		$.ajax({
			type: "POST",
			url: base_url + controller + '/generateIndent',
			data: {
				id: data.id,
				ref_id: data.ref_id,approve_type:data.approve_type
			}
		}).done(function(response) {
			$("#" + data.modal_id).modal();
			$("#" + data.modal_id + ' .modal-title').html(data.title);
			$("#" + data.modal_id + ' .modal-body').html(response);
			$("#" + data.modal_id + " .modal-body form").attr('id', data.form_id);
			$("#" + data.modal_id + " .modal-footer .btn-save").attr('onclick', "store('" + data.form_id + "','" + data.fnsave + "');");
			if (button == "close") {
				$("#" + data.modal_id + " .modal-footer .btn-close").show();
				$("#" + data.modal_id + " .modal-footer .btn-save").hide();
			} else if (button == "save") {
				$("#" + data.modal_id + " .modal-footer .btn-close").hide();
				$("#" + data.modal_id + " .modal-footer .btn-save").show();
			} else {
				$("#" + data.modal_id + " .modal-footer .btn-close").show();
				$("#" + data.modal_id + " .modal-footer .btn-save").show();
			}
			$(".single-select").comboSelect();
			initMultiSelect();
			setPlaceHolder();
		});
	}
</script>