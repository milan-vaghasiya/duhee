<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Payment Voucher</h4>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn waves-effect waves-light btn-outline-primary float-right addNew " data-button="both" data-modal_id="modal-lg" data-function="addPaymentVoucher" data-form_title="Add Payment "><i class="fa fa-plus"></i> Add Voucher</button>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='paymentVoucherTable' class="table table-bordered ssTable ssTable-cf" data-ninput='[0,1]' data-url='/getDtRows'></table>
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
	

	$(document).on("change","#entry_type",function(){       
        var entry_type = $("#entry_type").val();
        $(".entry_type").html("");
        if(entry_type != ''){
		    $.ajax({
				url : base_url + controller + '/getTransNo',
				type: 'post',
				data:{entry_type:entry_type},
				dataType:'json',
				success:function(data){                    
                    $("#trans_prefix").val(data.trans.trans_prefix);
                    $("#trans_no").val(data.trans.nextTransNo);
				}
			}); 
        }else{
            $(".entry_type").html("Entry Type is required.");
        }
    });

    $(document).on('change',"#opp_acc_id",function(){
        $("#ref_id").html('');
        $(".entry_type").html("");
        $(".opp_acc_id").html("");
        var entry_type = $("#entry_type").val();
        var party_id = $(this).val();
        if(entry_type != '' && party_id != ''){
		    $.ajax({
				url : base_url + controller + '/getReference',
				type: 'post',
				data:{entry_type:entry_type,party_id:party_id},
				dataType:'json',
				success:function(data){                    
                    $("#ref_id").html(data.referenceData);
                    $("#ref_id").comboSelect();
				}
			}); 
        }else{
            if(entry_type == ""){
                $(".entry_type").html("Entry Type is required.");
            }
            if(party_id == ""){
                $(".opp_acc_id").html("Party Name is required.");
            }
        }
        
    });
});
</script>