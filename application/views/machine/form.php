<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-2 form-group">
                <label for="part_no">General M/C No.</label>
                <input type="text" name="part_no" class="form-control numericOnly" value="<?=(!empty($dataRow->part_no))?$dataRow->part_no:""?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="item_code">Machine No.</label>
                <input type="text" name="item_code" class="form-control req" value="<?=(!empty($dataRow->item_code))?$dataRow->item_code:"";?>" />
            </div>
            <div class="col-md-2 form-group">
                <label for="make_brand">Make/Brand</label>
                <input type="text" name="make_brand" id="make_brand" class="form-control" value="<?=(!empty($dataRow->make_brand))?$dataRow->make_brand:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="typeof_machine">Machine Type</label>
                <select name="mTypeSelect" id="mTypeSelect" data-input_id="typeof_machine" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                        foreach ($machineTypes as $row) :
                            $selected = '';
                            if(!empty($dataRow->typeof_machine)){
                                if (in_array($row->id,explode(',',$dataRow->typeof_machine))) {
                                    $selected = "selected";
                                }
                            }
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->typeof_machine . '</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="typeof_machine" id="typeof_machine" value="<?=(!empty($dataRow->typeof_machine))?$dataRow->typeof_machine:"" ?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control single-select req">
                    <option value="0">Select</option>
                    <?php
                        foreach ($categoryList as $row) :
                            $selected = (!empty($dataRow->category_id) && $dataRow->category_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->category_name . '</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="item_name">Description</label>
                <input name="item_name" id="item_name" class="form-control" style="resize:none;" value="<?=(!empty($dataRow->item_name))?$dataRow->item_name:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="size">XYZ Working Capacity</label>
                <input type="text" name="size" id="size" class="form-control" value="<?=(!empty($dataRow->size))?$dataRow->size:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="wkg">M/C WEIGHT Kg.</label>
                <input type="text" name="wkg" class="form-control numericOnly" value="<?=(!empty($dataRow->wkg))?$dataRow->wkg:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="thread_type">Spindle</label>
                <input type="text" name="thread_type" id="thread_type" class="form-control" value="<?=(!empty($dataRow->thread_type))?$dataRow->thread_type:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="fg_id">Max Rapid Speed</label>
                <input type="text" name="fg_id" id="fg_id" class="form-control numericOnly" value="<?=(!empty($dataRow->fg_id))?$dataRow->fg_id:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="instrument_range">Spindle Speed Range</label>
                <input type="text" name="instrument_range" id="instrument_range" class="form-control" value="<?=(!empty($dataRow->instrument_range))?$dataRow->instrument_range:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="least_count">Spindle Power Torque</label>
                <input type="text" name="least_count" id="least_count" class="form-control" value="<?=(!empty($dataRow->least_count))?$dataRow->least_count:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="install_year">Installation Date</label>
                <input type="date" name="install_year" id="install_year" class="form-control" value="<?=(!empty($dataRow->install_year))?$dataRow->install_year: date('Y-m-d') ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="serial_prefix">Serial No.</label>
                <input type="text" name="serial_prefix" class="form-control" value="<?=(!empty($dataRow->serial_prefix))?$dataRow->serial_prefix:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="material_grade">Controller</label>
                <input type="text" name="material_grade" class="form-control" value="<?=(!empty($dataRow->material_grade))?$dataRow->material_grade:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="machine_hrcost">Hourly Cost</label>
                <input type="text" name="machine_hrcost" class="form-control floatOnly" value="<?=(!empty($dataRow->machine_hrcost))?$dataRow->machine_hrcost:""?>" />
            </div>
            <div class="col-md-3 form-group">
                <label for="prev_maint_req">Pre. Maintanance?</label>
                <select name="prev_maint_req" id="prev_maint_req" class="form-control" >
                    <option value="No" <?=(!empty($dataRow->prev_maint_req) && $dataRow->prev_maint_req == "No")?"selected":""?>>No</option>
                    <option value="Yes" <?=(!empty($dataRow->prev_maint_req) && $dataRow->prev_maint_req == "Yes")?"selected":""?>>Yes</option>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="location">Location</label>
                <input type="text" name="location" id="location" class="form-control" value="<?=(!empty($dataRow->location))?$dataRow->location:""?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="process_id">Process Name</label>
                <select name="processSelect" id="processSelect" data-input_id="process_id" class="form-control jp_multiselect req" multiple="multiple">
                    <?php
                        foreach ($processData as $row) :
                            $selected = '';
                            if(!empty($dataRow->process_id)){
                                if (in_array($row->id,explode(',',$dataRow->process_id))) {
                                    $selected = "selected";
                                }
                            }
                            echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->process_name . '</option>';
                        endforeach;
                    ?>
                </select>
                <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($dataRow->process_id))?$dataRow->process_id:"" ?>" />
            </div>
            <div class="col-md-3 form-group">
				<label for="material_spec">Authorized Person</label>
                <select id="emp" data-input_id="material_spec" class="form-control jp_multiselect" multiple="multiple">
                    <?php
                    foreach ($empData as $row) :
                        $selected='';$empArr = (!empty($dataRow->material_spec)) ? explode(',',$dataRow->material_spec) : array();
                        if(!empty($dataRow->material_spec) && in_array($row->id,$empArr)){$selected = "selected";}else{ $selected=''; }
                        echo '<option value="' . $row->id . '" ' . $selected . '>['.$row->emp_code.'] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
				<input type="hidden" name="material_spec" id="material_spec" value="" />
			</div>
            <div class="col-md-12 form-group">
                <label for="note">Notes</label>
                <textarea name="note" id="note" class="form-control" style="resize:none;"><?=(!empty($dataRow->note))?$dataRow->note:""?></textarea>
            </div>
        </div>
    </div>
</form>