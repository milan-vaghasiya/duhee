<form >
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : ""; ?>" />
           
            <div class="col-md-6 form-group">
                <label for="material_grade">Material Grade</label>
                <select id="multi_mtr_grade" data-input_id="material_grade" class="form-control jp_multiselect" multiple="multiple">
                    <option value="">Select Grade</option>
                    <?php
                    foreach ($materialGrades as $row) :
                        $selected = (!empty($dataRow->material_grade) && (in_array($row->id,explode(",",$dataRow->material_grade))))?"selected":"";
                        echo '<option value="' . $row->id . '" ' . $selected . '>' . $row->material_grade . '</option>';
                    endforeach;
                    ?>
                </select>
                <input type="hidden" name="material_grade" id="material_grade" value="" />
            </div>

            <div class="col-md-6 form-group">
                <label for="drawing_no">Drawing No</label>
                <input type="text" name="drawing_no" class="form-control" value="<?= (!empty($dataRow->drawing_no)) ? $dataRow->drawing_no : "" ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="rev_no">Revision No</label>
                <input type="text" name="rev_no" class="form-control" value="<?= (!empty($dataRow->rev_no)) ? $dataRow->rev_no : "" ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="app_rev_no">Applied Revision No</label>
                <input type="text" name="app_rev_no" class="form-control" value="<?= (!empty($dataRow->app_rev_no)) ? $dataRow->app_rev_no : "" ?>" />
            </div>
            
        </div>
    </div>
</form>