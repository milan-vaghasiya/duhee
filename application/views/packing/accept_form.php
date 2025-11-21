<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?=(!empty($dataRow->id))?$dataRow->id:""; ?>" />
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($jobData->job_card_id))?$jobData->job_card_id:""; ?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($jobData->next_approval_id))?$jobData->next_approval_id:""; ?>">
            <input type="hidden" name="job_trans_id" id="job_trans_id" value="<?=(!empty($jobData->id))?$jobData->id:""; ?>">
            <input type="hidden" name="fir_id" id="fir_id" value="<?=(!empty($jobData->ref_id))?$jobData->ref_id:""; ?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($jobData->product_id))?$jobData->product_id:""; ?>">
            <div class="col-md-12 form-group">
                <label for="qty">Qty</label>
				<input type="text" id="qty" name="qty" class="form-control req floatOnly"  value="<?=(!empty($dataRow->qty))?$dataRow->qty:''?>" />
            </div>
        </div>
    </div>
</form>