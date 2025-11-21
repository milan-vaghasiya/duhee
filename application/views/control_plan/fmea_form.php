<div class="col-md-12">
    <form id="potential_cause">
        <div class="row">
            <input type="hidden" name="id" value=" ">
            <input type="hidden" id="item_id" name="item_id" value="<?= !empty($dataRow->item_id) ? $dataRow->item_id : $item_id ?>">
            <input type="hidden" id="item_code" name="item_code" value="<?= !empty($itemData->item_code) ? $itemData->item_code : '' ?>">
       
            <div class="col-md-3 form-group">
                <label for="trans_number">Rev. No.</label>
                <input type="text" name="app_rev_no" class="form-control" value="0" readOnly>
            </div>
            <div class="col-md-3 form-group">
                <label for="app_rev_date">Date</label>
                <input type="date" name="app_rev_date" id="app_rev_date" class="form-control req" value="<?= !empty($dataRow->app_rev_date) ? $dataRow->app_rev_date : date("Y-m-d") ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="cust_rev_no">Cust. Rev. No.</label>
                <input type="text" name="cust_rev_no" id="cust_rev_no" class="form-control " value="<?= !empty($itemData->rev_no) ? $itemData->rev_no : '' ?>">
            </div>
        </div>
    </form>
</div>