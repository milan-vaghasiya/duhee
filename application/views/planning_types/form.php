<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            
            <div class="col-md-12 form-group">
                <label for="planning_type">Planning Types</label>
                <textarea name="planning_type" class="form-control req" rows="2"><?=(!empty($dataRow->planning_type)) ? $dataRow->planning_type:"" ?></textarea>
            </div>
        </div>
    </div>
</form>
