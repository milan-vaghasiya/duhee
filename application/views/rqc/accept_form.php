<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="" />
            <input type="hidden" name="mir_id" id="mir_id" value="<?=(!empty($job_card_id))?$job_card_id:""; ?>">
            <input type="hidden" name="mir_trans_id" id="mir_trans_id" value="<?=(!empty($job_trans_id))?$job_trans_id:""; ?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($job_approval_id))?$job_approval_id:""; ?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($product_id))?$product_id:""; ?>">
            <input type="hidden" name="process_id" id="process_id" value="<?=(!empty($process_id))?$process_id:""; ?>">
            <input type="hidden" name="trans_type" id="trans_type" value="4">

            <div class="col-md-6 form-group">
				<label for="trans_date">Date</label>
				<input type="date" id="trans_date" name="trans_date" class="form-control req" placeholder="dd-mm-yyyy" value="<?=(!empty($dataRow->trans_date))?$dataRow->trans_date:date("Y-m-d")?>" max="<?=date('Y-m-d')?>" />
			</div>
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
				<input type="text" id="lot_qty" name="lot_qty" class="form-control req floatOnly"  value="<?=(!empty($dataRow->lot_qty))?$dataRow->lot_qty:''?>" />
            </div>
        </div>
    </div>
</form>