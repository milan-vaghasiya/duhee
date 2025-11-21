<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($grnData->id))?$grnData->id:""; ?>" />
            <input type="hidden" name="location_id" id="id" value="<?=(!empty($grnData->location_id))?$grnData->location_id:""; ?>" />
            <div class="col-md-12 form-group">
                <label for="grn_date">Date</label>
                <input type="text" name="grn_date" class="form-control req" value="<?=(!empty($grnData->grn_date))?$grnData->grn_date:date("Y-m-d")?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="heat_no_insp">Heat No.</label>
                <input type="text" name="heat_no_insp" class="form-control req" value="<?=(!empty($grnData->heat_no_insp))?$grnData->heat_no_insp:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="forging_no">Forging No</label>
                <input type="text" name="forging_no" class="form-control" value="<?=(!empty($grnData->forging_no))?$grnData->forging_no:""?>" />
            </div>
            <div class="col-md-12 form-group">
                <label for="heat_treatment">Heat Treat. No.</label>
                <input type="text" name="heat_treatment" class="form-control" value="<?=(!empty($grnData->heat_treatment))?$grnData->heat_treatment:""?>" />
            </div>
        </div>
    </div>
</form>  