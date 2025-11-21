<?php $this->load->view('includes/header'); ?>
<div class="page-wrapper">
    <div class="container-fluid bg-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
							<div class="col-md-3">
                                <select name="stock_type" id="stock_type" class="form-control float-right">
                                    <option value="FRESH">Usable (Fresh)</option>
                                    <option value="USED">Usable (Used)</option>
                                    <option value="WIP">In Process</option>
                                </select>
                            </div>   
                            <div class="col-md-9">
                                <a href="<?= base_url($headData->controller.'/items') ?>" class="btn waves-effect waves-light btn-outline-dark float-right"><i class="fa fa-arrow-left"></i> Back</a>
                            </div>                            
                        </div>                                         
                    </div>
                    <div class="card-body reportDiv" style="min-height:75vh">
                        <div class="table-responsive">
                            <table id='reportTable' class="table table-bordered">
								<thead class="thead-info" id="theadData">
									<tr>
										<th>#</th>
										<th>Store</th>
										<th>Location</th>
										<th>Batch</th>
										<th>Current Stock</th>
									</tr>
								</thead>
								<tbody id="tbodyData"></tbody>
							</table>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>


<?php $this->load->view('includes/footer'); ?>
<script>
$(document).ready(function(){$(document).on('change','#stock_type',function(){loadItems();});});
function loadItems(){
    var item_id = '<?=$itemId?>';
    if(item_id){
        $.ajax({
            url: base_url + controller + '/getstockTransferData',
            data: {item_id:item_id,stock_type:$('#stock_type').val(),fdate:'',tdate:''},
            type: "POST",
            dataType:'json',
            success:function(data){
                $("#reportTable").dataTable().fnDestroy();
                $("#theadData").html(data.thead);
                $("#tbodyData").html(data.tbody);
                reportTable();
            }
        });
    }
}
</script>
<script src="<?php echo base_url();?>assets/js/custom/stock-transfer.js?v=<?=time()?>"></script>