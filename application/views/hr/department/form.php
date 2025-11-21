<form>
	<div class="col-md-12">
        <div class="row">
			<input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""?>" />
			
			<div class="col-md-12 form-group">
				<label for='name' class="control-label">Department Name</label>
				<input type="text" id="name" name="name" placeholder="Department Name" class="form-control req" value="<?=(!empty($dataRow->name))?$dataRow->name:""?>">				
			</div>
			
			<!--<div class="col-md-12 form-group">
				<label for="section">section</label>
                <select name="section" id="section" class="form-control single-select">
					<option value="">Select Section</option>
                    <?php
                    /*foreach ($sectionData as $section) :
                        $selected = (!empty($dataRow->section) && $dataRow->section == $section) ? "selected" : "";
                        echo '<option value="' . $section . '" ' . $selected . '>' . $section . '</option>';
                    endforeach;*/
                    ?>
                </select>
			</div>-->
            
			<div class="col-md-12 form-group">
				<label for="section">section</label>
                <select name="sectionSelect" id="sectionSelect" data-input_id="section" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                    foreach ($sectionData as $section) :
                        // $selected = (!empty($dataRow->section) && $dataRow->section == $section) ? "selected" : "";
                        $selected='';$sectionArr = (!empty($dataRow->section)) ? explode(',',$dataRow->section) : array();
                        if(!empty($dataRow->section) && in_array($section,$sectionArr)){$selected = "selected";}else{ $selected=''; }
                        echo '<option value="' . $section . '" ' . $selected . '>' . $section . '</option>';
                    endforeach;
                    ?>
                </select>
				<input type="hidden" name="section" id="section" value="" />
			</div>

			<!--<div class="col-md-12 form-group">
				<label for="empSelect">Select Employees who have rights to Apptove Leave </label>
                <select name="empSelect" id="empSelect" data-input_id="leave_authorities" class="form-control jp_multiselect" multiple="multiple">
                    <?php						
                        /*foreach($empData as $row):
							$selected='';$leave_auth = (!empty($dataRow->leave_authorities)) ? explode(',',$dataRow->leave_authorities) : array();
                            if(!empty($dataRow->leave_authorities) && in_array($row->id,$leave_auth)){$selected = "selected";}else{ $selected=''; }
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->emp_name.'</option>';
                        endforeach;*/
                    ?>
                </select>
				<input type="hidden" name="leave_authorities" id="leave_authorities" value="" />
			</div>-->
		</div>
	</div>	
</form>
            
