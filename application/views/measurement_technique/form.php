<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            
            <div class="col-md-12 form-group">
                <label for="measurement_technique">Measurement Technique</label>
                <input type="text" name="measurement_technique" id="measurement_technique" class="form-control req" value="<?= (!empty($dataRow->measurement_technique)) ? $dataRow->measurement_technique : "" ?>">
            </div>
            <div class="col-md-12 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?= (!empty($dataRow->remark)) ? $dataRow->remark : "" ?>">
            </div>
        </div>
    </div>
</form>