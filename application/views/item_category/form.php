<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="maincate_level" id="maincate_level" value="" />
            <input type="hidden" name="category_type" id="category_type" value="<?=(!empty($dataRow->category_type))?$dataRow->category_type:""; ?>" />
			<div class="col-md-8 form-group">
                <label for="category_name">Category Name</label>
                <input type="text" name="category_name" class="form-control req" value="<?=(!empty($dataRow->category_name))?$dataRow->category_name:""?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="final_category">Final Category</label>
                <select name="final_category" id="final_category" class="form-control single-select">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->final_category == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->final_category == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>
			<div class="col-md-8 form-group">
                <label for="ref_id">Main Category</label>
                <select name="ref_id" id="ref_id" class="form-control select2 req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($mainCategory as $row) :
                            if($row->id != $dataRow->id):
                                $selected = (!empty($dataRow->ref_id) && $dataRow->ref_id == $row->id) ? "selected" : "";
                                echo '<option value="' . $row->id . '" class="level_'.$row->category_level.'" data-level="'.$row->category_level.'" data-category_type="'.$row->category_type.'" ' . $selected . '>' . $row->category_name . '</option>';
                            endif;
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group returnable">
                <label for="is_return">Returnable</label>
                <select name="is_return" id="is_return" class="form-control single-select">
                    <option value="0" <?=(!empty($dataRow) && $dataRow->is_return == 0) ? "selected" : "";?>>No</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->is_return == 1) ? "selected" : "";?>>Yes</option>
                </select>
            </div>
            <div class="col-md-4 form-group toolType">
                <label for="tool_type">Code</label>
                <input type="text" name="tool_type" class="form-control req" value="<?=(!empty($dataRow->tool_type))?$dataRow->tool_type:""?>" />
                <input type="hidden" name="is_return" id="is_return" value="<?=(!empty($dataRow->is_return)) ? $dataRow->is_return : 1;?>" />
            </div>
            <div class="col-md-12 form-group">
				<label for="auth_detail">Authorized Person</label>
                <select  id="emp" data-input_id="auth_detail" class="form-control jp_multiselect" multiple="multiple">
                    <!-- <option value="1" selected>Admin</option> -->
                    <?php
                    foreach ($empData as $row) :
                        $selected='';$empArr = (!empty($dataRow->auth_detail)) ? explode(',',$dataRow->auth_detail) : array();
                        if(!empty($dataRow->auth_detail) && in_array($row->id,$empArr)){$selected = "selected";}else{ $selected=''; }
                        echo '<option value="' . $row->id . '" ' . $selected . '>['.$row->emp_code.'] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
				<input type="hidden" name="auth_detail" id="auth_detail" value="<?=(!empty($dataRow->auth_detail))?$dataRow->auth_detail:"" ?>" />
			</div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" class="form-control" rows="3" ><?=(!empty($dataRow->remark))?$dataRow->remark:"";?></textarea>
            </div>
        </div>
    </div>
</form>
<!-- Create By : Karmi  -->
<script type="text/javascript">
// var cid = '<?=(isset($catId)) ? $catId : 0?>';
// console.log(cid);
// if(cid != 0){ $('#category_type').val(cid); initModalSelect(); }
$(document).ready(function(){
    var ctype = $('#category_type').val();
    if(ctype == 6 || ctype == 7){$('.toolType').show();$('.returnable').hide();}
	else{$('.returnable').show();$('.toolType').hide();}
    $(document).on('change','#ref_id',function(){
		var ref_id = $(this).val();
		var level = $('#ref_id :selected').data('level'); 
		var category_type = $('#ref_id :selected').data('category_type'); 
        $('#maincate_level').val(level);
		$('#category_type').val(category_type);
		
		if(category_type == 6 || category_type == 7){$('.toolType').show();$('.returnable').hide();}
		else{$('.returnable').show();$('.toolType').hide();}
	});
});
</script>


