<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
            <input type="hidden" name="material_type" id="material_type" value="<?=(!empty($dataRow->material_type))?$dataRow->material_type:""?>" />
            <input type="hidden" name="job_card_id" id="job_card_id" value="0" />

            <div class="col-md-12 form-group req">
                <label for="req_item_id">Item Name</label>
                <select name="req_item_id" id="req_item_id" class="form-control req">
                    <option value="">Select Item</option>
                    <?php 
                        foreach($itemData as $row):
                            echo '<option value="'.$row->id.'" data-item_type="'.$row->item_type.'">'.$row->item_name.' </option>';
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="req_date">Request Date</label>
                <input type="req_date" name="req_date" id="req_date" class="form-control req" max="<?=date("Y-m-d")?>" value="<?=date("Y-m-d")?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="req_qty">Request Qty.</label>
                <input type="number" name="req_qty" id="req_qty" class="form-control floatOnly req" min="0" value="<?=(!empty($dataRow))?(($dataRow->req_qty != "0.000")?$dataRow->req_qty:$dataRow->req_qty):""?>">                
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>">
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function(){
	$(document).on('change','#req_item_id', function(){
        $('#material_type').val($(this).find(":selected").data('item_type'));
    });
});
</script>