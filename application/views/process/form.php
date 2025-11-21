<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="process_name">Process Name</label>
                <input type="text" name="process_name" class="form-control req" value="<?=(!empty($dataRow->process_name))?$dataRow->process_name:"";?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="dept_id">Department</label>
                <select name="dept_id" id="dept_id" class="form-control single-select req">
                    <option value="">Select Department</option>
                    <?php
                        foreach($deptRows as $row):
                            $selected = (!empty($dataRow->dept_id) && $row->id == $dataRow->dept_id)?"selected":"";
                            echo '<option value="'.$row->id.'" '.$selected.'>'.$row->name.'</option>';
                        endforeach;
                    ?>
                </select>
                <div class="error dept_id"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="process_by">Process By</label>
                <select name="process_by" id="process_by" class="form-control req">
                    <option value="">Select</option>
                    <option value="1" <?=(!empty($dataRow->process_by) && $dataRow->process_by == 1)?'selected':''?>>Inhouse</option>
                    <option value="2" <?=(!empty($dataRow->process_by) && $dataRow->process_by == 2)?'selected':''?>>Vendor</option>
                    <option value="3" <?=(!empty($dataRow->process_by) && $dataRow->process_by == 3)?'selected':''?>>Both</option>
                </select>
                <div class="error process_by"></div>
            </div>
            
            <div class="col-md-6 form-group">
                <label for="mfg_by">Mfg. By</label>
                <select name="mfg_by" id="mfg_by" class="form-control req">
                    <option value="0">Any</option>
                    <option value="1" <?=(!empty($dataRow->mfg_by) && $dataRow->mfg_by == 1)?'selected':''?>>Root 1</option>
                    <option value="2" <?=(!empty($dataRow->mfg_by) && $dataRow->mfg_by == 2)?'selected':''?>>Root 2</option>
                </select>
                <div class="error process_by"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="internal_heat">Internal Heat Treatment</label>
                <select name="internal_heat" id="internal_heat" class="form-control req">
                    <option value="0" <?=(empty($dataRow->internal_heat))?'selected':''?>>No</option>
                    <option value="1" <?=(!empty($dataRow->internal_heat) && $dataRow->internal_heat == 1)?'selected':''?>>Yes</option>
                </select>
                <div class="error internal_heat"></div>
            </div>

            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <textarea name="remark" id="remark" class="form-control"><?=(!empty($dataRow->remark))?$dataRow->remark:""?></textarea>
            </div>
        </div>
    </div>
</form>