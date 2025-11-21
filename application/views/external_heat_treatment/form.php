<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>"/>
                
            <div class="col-md-6 form-group">
                <label for="item_id">Products</label>
                <select name="item_id" id="item_id" class="form-control single-select req">
                    <option value="">Select</option>
                    <?php
                        foreach ($itemList as $row):
                            $selected = (!empty($dataRow->item_id) && $dataRow->item_id == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" ' . $selected . '>['.$row->item_code.'] '.$row->part_no.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="case_aim">Case Aim</label>
                <input type="text" name="case_aim" class="form-control" value="<?=(!empty($dataRow->case_aim))?$dataRow->case_aim:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="carb_drg_no">Carb Drawing No</label>
                <input type="text" name="carb_drg_no" class="form-control" value="<?= (!empty($dataRow->carb_drg_no)) ? $dataRow->carb_drg_no : "" ?>" />
            </div>

            <div class="col-md-6 form-group">
                <label for="carb_rev_no">Carb Revision No</label>
                <input type="text" name="carb_rev_no" class="form-control" value="<?= (!empty($dataRow->carb_rev_no)) ? $dataRow->carb_rev_no : "" ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="atl_case_aim">Alt. Case Aim</label>
                <input type="text" name="atl_case_aim" class="form-control" value="<?=(!empty($dataRow->atl_case_aim))?$dataRow->atl_case_aim:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="depth_mm">Stackmark Depth MM</label>
                <input type="text" name="depth_mm" id="depth_mm" class="form-control" value="<?=(!empty($dataRow->depth_mm))?$dataRow->depth_mm:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="od_mm">OD (in mm)</label>
                <input type="text" name="od_mm" id="od_mm" class="form-control" value="<?=(!empty($dataRow->od_mm))?$dataRow->od_mm:""; ?>" />
            </div>
            <div class="col-md-6 form-group">
                <label for="ht_mm">Ht (in mm)</label>
                <input type="text" name="ht_mm" id="ht_mm" class="form-control" value="<?=(!empty($dataRow->ht_mm))?$dataRow->ht_mm:""; ?>" />
            </div>
            <!-- <div class="col-md-6 form-group">
                <label for="gross_wt">GS wt/pc </label>
                <input type="text" name="gross_wt" id="gross_wt" class="form-control" value="<?=(!empty($dataRow->gross_wt))?$dataRow->gross_wt:""; ?>" />
            </div> -->
            <div class="col-md-12 form-group">
                <label for="section">A Section</label>
                <input type="text" name="section" id="section" class="form-control" value="<?=(!empty($dataRow->section))?$dataRow->section:""; ?>" />
            </div>
        </div>
    </div>
</form>

