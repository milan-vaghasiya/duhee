<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="control_method">Control Method (Short Name)</label>
                <input name="control_method" class="form-control text-uppercase req"  value="<?=(!empty($dataRow->control_method))?$dataRow->control_method:"";?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="cm_alias">ALias Name</label>
                <input name="cm_alias" class="form-control" value="<?=(!empty($dataRow->cm_alias))?$dataRow->cm_alias:"";?>" />
            </div>
            
        </div>
    </div>
</form>