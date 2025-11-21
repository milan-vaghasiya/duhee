<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <div class="col-md-6 form-group">
                <label for="furnace_type">Furnace Type</label>
                <select name="furnace_type" id="furnace_type" class="form-control single-select">
                    <option>Select</option>
                    <option value="1" <?=(!empty($dataRow) && $dataRow->furnace_type == 1)?"selected":""?>>Hardening</option>
                    <option value="2" <?=(!empty($dataRow) && $dataRow->furnace_type == 2)?"selected":""?>>Tempering</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="furnace_no">Furnace No</label>
                <input type="text" name="furnace_no" class="form-control req" value="<?=(!empty($dataRow->furnace_no))?$dataRow->furnace_no:""?>" />
            </div>
            <div class="col-md-12 form-group"">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:""?>" />
            </div>
        </div>
    </div>
</form>