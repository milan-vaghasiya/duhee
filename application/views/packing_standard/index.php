<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Packing Standard</h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='packingStandardTable' class="table table-bordered ssTable" data-url='/getStandardDTRows'></table>
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
    $(document).on('click',".packingStandard",function(){
        var item_id = $(this).data('id');
        var itemName = $(this).data('item_code');
        var wt_pcs = $(this).data('wt_pcs');
        var functionName = $(this).data("function");
        var modalId = $(this).data('modal_id');
        var button = $(this).data('button');
		var title = $(this).data('form_title'); 
		var formId = functionName;

        $.ajax({ 
            type: "POST",   
            url: base_url + controller + '/' + functionName,   
            data: {item_id:item_id}
        }).done(function(response){
            $("#"+modalId).modal();
			$("#"+modalId+' .modal-title').html(title + " [ Product : "+itemName+" ]");
            $("#"+modalId+' .modal-body').html(response);
            $("#"+modalId+" .modal-body form").attr('id',formId);
            $("#"+modalId+" .modal-footer .btn-save").attr('onclick','savePackingStandard("'+formId+'");');    
            if(button == "close"){
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").hide();
            }else if(button == "save"){
                $("#"+modalId+" .modal-footer .btn-close").hide();
                $("#"+modalId+" .modal-footer .btn-save").show();
            }else{
                $("#"+modalId+" .modal-footer .btn-close").show();
                $("#"+modalId+" .modal-footer .btn-save").show();
                $("#"+modalId+" .modal-footer .btn-edit").hide();
            } 
            $("#item_id").val(item_id);  
            $("#wt_pcs").val(wt_pcs);  
            $(".modal-lg").attr("style","max-width: 70% !important;");
			$(".single-select").comboSelect();
            setPlaceHolder();
        });
    });
});

function savePackingStandard(formId,fnsave,srposition=0){
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
			initTable(srposition); //$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
            
            $('#stadardBody').html("");
            $('#stadardBody').html(data.tbody);
            
            $('#qty_per_box').val("");
            $('#wt_per_box').val("");
            $('#box_id').val("");
            $('#box_id').comboSelect();
        }else{
			initTable(srposition); //$('#'+formId)[0].reset();$(".modal").modal('hide');
			toastr.error(data.message, 'Error', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
		}
				
	});
}

function trashPackingStandard(id,item_id,name='Record'){
	var send_data = { id:id, item_id:item_id };
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
						url: base_url + controller + '/deletePackingStandard',
						data: send_data,
						type: "POST",
						dataType:"json",
						success:function(data){
							if(data.status==0){
								toastr.error(data.message, 'Sorry...!', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							}else{
								initTable(0); //initMultiSelect();
								toastr.success(data.message, 'Success', { "showMethod": "slideDown", "hideMethod": "slideUp", "closeButton": true, positionClass: 'toastr toast-bottom-center', containerId: 'toast-bottom-center', "progressBar": true });
							
                                $('#stadardBody').html("");
                                $('#stadardBody').html(data.tbody);
                            }
						}
					});
				}
			},
			cancel: {
                btnClass: 'btn waves-effect waves-light btn-outline-secondary',
                action: function(){ }
            }
		}
	});
}
</script>