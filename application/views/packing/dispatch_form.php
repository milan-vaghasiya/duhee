<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="trans_main_id" value="<?=(!empty($dataRow->trans_main_id)?$dataRow->trans_main_id:0)?>" >
            <input type="hidden" name="trans_child_id" value="<?=(!empty($dataRow->id)?$dataRow->id:0)?>" >
            <input type="hidden" name="item_id" value="<?=(!empty($dataRow->item_id)?$dataRow->item_id:0)?>" >

            <div class="col-md-4">
                <label for="dc_no">DC No.</label>
                <div class="input-group">
                    <input type="text" class="form-control" name="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:''?>" readonly />
                    <input type="text" class="form-control" name="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:''?>" readonly />
                </div>
            </div>
            <div class="col-md-4">
                <label for="dc_date">DC Date</label>
                <input type="date" class="form-control" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date('Y-m-d')?>" readonly />
            </div>
            <div class="col-md-4">
                <label for="dc_date">Challan Qty.</label>
                <input type="text" class="form-control" name="challan_qty" value="<?=(!empty($dataRow->qty))?$dataRow->qty:'0'?>" readonly />
            </div>
        </div>
        <div class="row mt-2">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-info">
                        <tr>
                            <th>#</th>
                            <th>Location</th>
                            <th class="text-center">Batch No.</th>
                            <th class="text-center">Stock Qty.</th>
                            <th class="text-center">Box Detail<br><small>(Qty x Box)</small></th>
                            <th>Dispatch Qty.</th>
                        </tr>
                    </thead>
                    <tbody id="batchData">
                        <?php
                            if(!empty($batchData['batchData'])):
                                echo $batchData['batchData'];
                            else:
                                echo '<tr>
                                    <td colspan="6" class="text-center">No data available in table</td>
                                </tr>';
                            endif;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">Total</td>
                            <td><input type="text" id="totalQty" name="totalQty" class="form-control req" value="0" readOnly></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>                    
</form>
<script>
$(document).ready(function(){
	$(document).on('keyup change',".batchQty",function(){		
		var batchQtyArr = $("input[name='batch_quantity[]']").map(function(){return $(this).val();}).get();
		var batchQtySum = 0;
		$.each(batchQtyArr,function(){batchQtySum += parseFloat(this) || 0;});
		$('#totalQty').val("");
		$('#totalQty').val(batchQtySum.toFixed(3));
		$("#qty").val(batchQtySum.toFixed(3));

		var id = $(this).data('rowid');
		var cl_stock = $(this).data('cl_stock');
		var batchQty = $(this).val();
		$(".batch_qty"+id).html("");
		$(".qty").html();
		if(parseFloat(batchQty) > parseFloat(cl_stock)){
			$(".batch_qty"+id).html("Stock not avalible.");
		}
	});
});
</script>