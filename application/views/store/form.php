<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="mainstore_level" id="mainstore_level" value="" />
            
            <div class="col-md-8 form-group">
                <label for="location">Rack</label>
                <input type="text" name="location" class="form-control req" value="<?=(!empty($dataRow->location))?$dataRow->location:""; ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="final_location">Final Store</label>
                <select name="final_location" id="final_location" class="form-control single-select">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->final_location == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->final_location == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>

            <div class="col-md-8 form-group">
                <label for="ref_id">Store Name</label>
                <select name="ref_id" id="ref_id" class="form-control single-select req" tabindex="-1">
                <option value="">Select Store</option>
                        <?php
                        
                        if(!empty($storeNames)):

                            foreach($storeNames as $row):
                                $selected = (!empty($dataRow->ref_id) && $dataRow->ref_id == $row->id)?"selected":"";
                                echo '<option value="' . $row->id . '" class="level_'.$row->store_level.'" data-level="'.$row->store_level.'" data-store_name="'.$row->location.'"' . $selected . '>' . $row->location . '</option>';

                            endforeach;
                        endif;
                    
                        ?>
                </select>
                <input type="hidden" id="storename" name="storename" value="<?=(!empty($dataRow->store_name))?$dataRow->store_name:"" ?>" />
            </div>
            <div class="col-md-4 form-group">
            <label for="prd_movement">Prod. Movement</label>
                <select name="prd_movement" id="prd_movement" class="form-control" >
                    <option value="0" <?=empty($dataRow->prd_movement)?'selected':''?>>No</option>
                    <option value="1" <?=!empty($dataRow->prd_movement)?'selected':''?>>Yes</option>
                       
                </select>
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" rows="2" class="form-control"></textarea>
            </div>
        </div>
    </div>
</form>
<!-- Create By : Karmi  -->
<script type="text/javascript">
$(document).ready(function(){
    $(document).on('change','#ref_id',function(){
		var ref_id = $(this).val();
		var level = $(this).find(":selected").data('level'); 
		var store_name = $(this).find(":selected").data('store_name');
        $('#mainstore_level').val(level);
        $('#storename').val(store_name);
	});
});
</script>