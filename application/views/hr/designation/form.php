<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='title' class="control-label">Designation Name</label>
				<input type="text" id="title" name="title" placeholder="Designation Name" class="form-control req" value="<?=(!empty($dataRow->title))?$dataRow->title:""?>">				
			</div>

			<!--<div class="col-md-12 form-group">
                <label for='dept_id' class="control-label">Department Name</label>
                <select name="dept_id" id="dept_id" class="form-control single-select req">
					<option value="">Select Department</option>
                    <?php
                    /*foreach ($deptData as $row) :
                        $selected = (!empty($dataRow->dept_id) && $dataRow->dept_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->name . '</option>';
                    endforeach;*/
                    ?>
                </select>
            </div>-->
			
            <div class="col-md-12 form-group">
                <label for='description' class="control-label">Remark</label>
                <textarea name="description" class="form-control" placeholder="Remark" style="resize:none;" rows="1"><?=(!empty($dataRow->description))?$dataRow->description:""?></textarea>
            </div>
		</div>
	</div>	
</form>
            
