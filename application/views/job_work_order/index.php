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
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"> <button onclick="statusTab('jobOrderTable',1);" class=" btn waves-effect waves-light btn-outline-info active" style="outline:0px" data-toggle="tab" aria-expanded="false">Active</button> </li>
                                        <li class="nav-item"> <button onclick="statusTab('jobOrderTable',0);" class=" btn waves-effect waves-light btn-outline-danger" style="outline:0px" data-toggle="tab" aria-expanded="false">De-Active</button> </li>
                                    </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title">Job Work Order</h4>
                            </div>
                            <div class="col-md-4">
                                <a href="<?=base_url($headData->controller."/addOrder")?>" class="btn waves-effect waves-light btn-outline-primary float-right"><i class="fa fa-plus"></i> Add Order</a>
                                <!-- <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew permission-write" data-button="both" data-modal_id="modal-lg" data-function="addOrder" data-form_title="Add Job Work Order"><i class="fa fa-plus"></i> Add Order</button> -->
                            </div>                             
                        </div>                                                                              
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='jobOrderTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0,1]' data-url='/getDTRows'></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>   
</div>

<?php $this->load->view('includes/footer'); ?>
<script src="<?php echo base_url();?>assets/js/custom/jobwork-order.js?v=<?=time()?>"></script>
<script>
$(document).ready(function(){
	$(document).on('change','#rate_per',function(e){
		$(".error").html("");
		var rate_per = $(this).val();
		var rate = $('#rate').val();
       
        if(rate_per == 1){

            if($('#qty').val() != 0 && $('#qty').val() != "")
            {
                var perpcs = rate * $('#qty').val();
                $("#amount").val(perpcs); 
            } else { $(".qty_pcs").html("Qty Pcs is required."); $("#amount").val(0); } 

        } else if(rate_per == 2) {

            if($('#qty_kg').val() != 0 && $('#qty_kg').val() != "")
            {
                var perkg = rate * $('#qty_kg').val();
                $("#amount").val(perkg);
            } else { $(".qty_kg").html("Qty kg is required."); $("#amount").val(0); } 

        } else {
            $("#amount").val(0); 
        }
    });
    
	$(document).on("click",".closeTerms",function(){
        $("#termModel").modal('hide');
    });

    $(document).on('change',"#vendor_id",function(){
        var vendor_id = $(this).val();
        if(vendor_id == ""){
            $("#processSelect").html("");
            $("#process_id").val("");
            reInitMultiSelect();
        }else{
            $.ajax({
                url: base_url + controller + "/getVendorProcessList",
                type: "POST",
                data:{vendor_id:vendor_id},
                dataType:"json",
                success:function(data){
                    $("#processSelect").html(data.options);
                    $("#process_id").val("");
                    reInitMultiSelect();
                }
            });
        }
    });
});
</script>