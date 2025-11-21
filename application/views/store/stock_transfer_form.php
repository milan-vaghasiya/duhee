<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" name="batch_no" id="batch_no" class="form-control" value="<?=(!empty($dataRow))?$dataRow['batch_no']:""?>" readonly />
                <input type="hidden" name="from_location_id" id="from_location_id" value="<?=(!empty($dataRow))?$dataRow['location_id']:""?>">
                <input type="hidden" name="item_id" id="item_id" value="<?=(!empty($dataRow))?$dataRow['item_id']:""?>">
            </div>
            <div class="col-md-6 form-group">
                <label for="stock_qty">Stock Qty.</label>
                <input type="text" id="stock_qty" class="form-control" value="<?=(!empty($dataRow))?$dataRow['stock_qty']:""?>" readonly />
            </div>
            <div class="col-md-6 form-group">
                <label for="to_location_id">Store Location</label>
                <select name="to_location_id" id="to_location_id" class="form-control model-select2 req">
                    <option value="">Select Location</option>
                    <?php
                        if(!empty($locationData)):
                            foreach($locationData as $key=>$option): ?>
                                <optgroup label="<?= $key; ?>">
                                <?php foreach($option as $val): ?>
                                        <option value="<?= $val->id; ?>"><?= $val->location; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                        <?php   endforeach; 
                        endif;
                    ?>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label for="transfer_qty">Qty.</label>
                <input type="number" name="transfer_qty" id="transfer_qty" class="form-control floatOnly req" value="" />
            </div>
        </div>
    </div>
</form>