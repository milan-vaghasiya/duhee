<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:"" ?>" />
            
            <div class="col-md-12 form-group">
                <label for="bl_no">B.L. No</label>
                <input type="text" name="bl_no" class="form-control req" value="<?=(!empty($dataRow->quote_rev_no))?$dataRow->quote_rev_no:""?>" />
            </div>

            <div class="col-md-12 form-group">
                <label for="bl_date">B.L. Date</label>
                <input type="date" name="bl_date" class="form-control req" value="<?=(!empty($dataRow->delivery_date))?$dataRow->delivery_date:""?>" />
            </div>
        </div>
    </div>
</form>