<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Product Options</h4>
                            </div>
                            <div class="col-md-6">
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-md" data-function="addCycleTime" data-form_title="Add Cycle Time"><i class="fa fa-plus"></i> Add Cycle Time</button> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='productOptionTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/product.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/item-stock-update.js?v=<?=time()?>"></script>
<script src="<?php echo base_url();?>assets/js/custom/tool_consumption.js?v=<?=time()?>"></script>

<script>
    $(document).ready(function() {
	
        $(document).on('click', ".addProductOption", function() {
            var id = $(this).data('id');
            var productname = $(this).data('product_name');
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;
			var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}

            $.ajax({
                type: "POST",
                url: base_url + 'productOption/' + functionName,
                data: {id: id}
            }).done(function(response) {
                $("#" + modalId).modal();
				// $("#" + modalId + " .modal-dialog").css('max-width','50%');
                $("#" + modalId + ' .modal-title').html(title);
                $("#" + modalId + ' .modal-body').html(response);
                $("#" + modalId + " .modal-body form").attr('id', formId);
				$("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave+"');");
                if (button == "close") {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#" + modalId + " .modal-footer .btn-close").hide();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").show();
                }
				$(".inputmask-his").inputmask("99:99:99");
				$(".single-select").comboSelect();setPlaceHolder();
            });
        });
		
		$(document).on('click', ".addInspectionOption", function() {
            var id = $(this).data('id');
            var param_type = $(this).data('param_type');
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
            var title = $(this).data('form_title');
            var formId = functionName;
            var fnsave = $(this).data("fnsave");if(fnsave == "" || fnsave == null){fnsave="save";}

            $.ajax({
                    type: "POST",
                    url: base_url + 'productOption/' + functionName,
                    data: {id: id,param_type:param_type}

            }).done(function(response) {
                $("#" + modalId).modal();
                $("#" + modalId + " .modal-dialog").css('max-width','50%');
                $("#" + modalId + ' .modal-title').html(title);
                $("#" + modalId + ' .modal-body').html(response);
                $("#" + modalId + " .modal-body form").attr('id', formId);
                $("#" + modalId + " .modal-footer .btn-save").attr('onclick', "store('" + formId + "', '"+fnsave+"');");
                if (button == "close") {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else if (button == "save") {
                    $("#" + modalId + " .modal-footer .btn-close").hide();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                } else {
                    $("#" + modalId + " .modal-footer .btn-close").show();
                    $("#" + modalId + " .modal-footer .btn-save").hide();
                }
                $(".single-select").comboSelect();setPlaceHolder();
            });
        });
        
        $(document).on('click','.addProductProcess',function(){
            var id = $(this).data('id');
            var itemName = $(this).data('product_name');
            var functionName = $(this).data("function");
            var modalId = $(this).data('modal_id');
            var button = $(this).data('button');
    		var title = $(this).data('form_title');
    		var formId = functionName;
    
            $.ajax({ 
                type: "POST",   
                url: base_url + controller + '/' + functionName,   
                data: {id:id}
            }).done(function(response){
                $("#"+modalId).modal();
    			$("#"+modalId+' .modal-title').html(title);
                $("#"+modalId+' .modal-body').html(response);
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
    			$(".single-select").comboSelect();
               	setPlaceHolder();
    		});
    	});
    });
</script>