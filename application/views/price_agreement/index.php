<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="card-title">Price Agreement</h4>
                            </div>
                            <div class="col-md-6">
                                <a href="<?=base_url($headData->controller."/addPurchaseOrder")?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Order</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='priceAgreementTable' class="table table-bordered ssTable" data-url='/getDTRows'></table>
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
                <h4 class="modal-title" id="exampleModalLabel1">Create GRN</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="<?=base_url("grn/createGrn");?>">
                <div class="modal-body">
                    <div class="col-md-12"><b>Party Name : <span id="partyName"></span></b></div>
                    <input type="hidden" name="party_id" id="party_id" value="">
                    <input type="hidden" name="party_name" id="party_name" value="">
                    <div class="col-md-12">
                        <div class="error general"></div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-info">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">PO. No.</th>
                                        <th class="text-center">PO. Date</th>
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
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create GRN</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){
    $(document).on('click','.createGrn',function(){
        var party_id = $(this).data('party_id');
		var party_name = $(this).data('party_name');

		$.ajax({
			url : base_url + controller + '/getPartyOrders',
			type: 'post',
			data:{party_id:party_id},
			dataType:'json',
			success:function(data){
				$("#orderModal").modal();				
				$("#partyName").html(party_name);
				$("#party_name").val(party_name);
				$("#party_id").val(party_id);
				$("#orderData").html("");
				$("#orderData").html(data.htmlData);
			}
		});
    });
});
</script>