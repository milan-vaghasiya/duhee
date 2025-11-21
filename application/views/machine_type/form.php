<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:"" ?>" />
   
            <div class="col-md-12 form-group">
                <label for="typeof_machine">Machine Type</label>
                <input type="text" name="typeof_machine" class="form-control req"  value="<?=(!empty($dataRow->typeof_machine))?$dataRow->typeof_machine:""?>" />
            </div>
        </div>
    </div>
</form>