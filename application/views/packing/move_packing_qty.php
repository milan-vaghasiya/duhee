<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=(!empty($job_card_id))?$job_card_id:""; ?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=(!empty($job_approval_id))?$job_approval_id:""; ?>">
            <input type="hidden" name="batch_no" id="batch_no" value="<?=(!empty($batch_no))?$batch_no:""; ?>">
            <input type="hidden" name="pending_qty" id="pending_qty" value="<?=(!empty($pending_qty))?$pending_qty:""; ?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($item_id))?$item_id:""; ?>">
            <div class="col-md-6 form-group">
                <label for="location_id">Location</label>
				<select id="location_id" name="location_id" class="form-control req single-select"  >
                    <option value="">Select Location</option>
                    <?php
                        if(!empty($packStoreList)){
                            foreach($packStoreList as $row){
                                ?><option value="<?=$row->id?>"><?=$row->location?></option><?php
                            }
                        }
                    ?>
                </select>
                <div class="error location_id"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="qty">Qty</label>
				<input type="text" id="qty" name="qty" class="form-control req floatOnly"  value="" />
            </div>
        </div>
    </div>
</form>