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
                                    <li class="nav-item"> <button onclick="statusTabPO('purchaseOrderTable',0);" class="nav-link btn waves-effect waves-light btn-outline-info active" data-toggle="tab" aria-expanded="false">Pending</button> </li>
                                    <li class="nav-item"> <button onclick="statusTabPO('purchaseOrderTable',1);" class="nav-link btn waves-effect waves-light btn-outline-success" data-toggle="tab" aria-expanded="false">Completed</button> </li>
                                </ul>
                            </div>
                            <div class="col-md-4">
                                <h4 class="card-title text-center"><?=(!empty($order_type)?'RM Purchase Order':'Purchase Order')?></h4>
                            </div>
                            <div class="col-md-4">
                                <a href="<?=base_url($headData->controller."/addPurchaseOrder/".$order_type)?>" class="btn waves-effect waves-light btn-outline-primary float-right permission-write"><i class="fa fa-plus"></i> Add Order</a>
                            </div>                             
                        </div>                                         
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id='purchaseOrderTable' class="table table-bordered ssTable" data-url='/getDTRows/0/<?=$order_type?>'></table>
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
                <h4 class="modal-title" id="exampleModalLabel1">Create GIR</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="party_so" method="post" action="<?=base_url("gir/createGir");?>">
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
                    <button type="submit" class="btn waves-effect waves-light btn-outline-success" id="btn-create"><i class="fa fa-check"></i> Create GIR</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->load->view('includes/footer'); ?>
<script>

function statusTabPO(tableId,status,srnoPosition=1){
    $("#"+tableId).attr("data-url",'/getDTRows/'+status+'/'+<?=$order_type?>);
    ssTable.state.clear();initTable(srnoPosition);
}

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