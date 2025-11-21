<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=$allocatedMaterial->id?>">
            <input type="hidden" name="ref_no" id="ref_no" value="<?=$allocatedMaterial->ref_no?>">
            <input type="hidden" name="item_id" id="item_id" value="<?=$allocatedMaterial->item_id?>">
            <input type="hidden" name="location_id" id="location_id" value="<?=$allocatedMaterial->location_id?>">
            <input type="hidden" name="ref_batch" id="ref_batch" value="<?=$allocatedMaterial->ref_batch?>">
            <div class="col-md-12 form-group">
                <label for="item_name">Item Name</label>
                <input type="text" id="item_name" class="form-control" value="<?=$allocatedMaterial->item_full_name?>" readonly>
            </div>

            <div class="col-md-12 form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" value="<?="[".$allocatedMaterial->store_name."]".$allocatedMaterial->location?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="batch_no">Batch No.</label>
                <input type="text" class="form-control" value="<?=$allocatedMaterial->batch_no?>" readonly>
            </div>

            <div class="col-md-6 form-group">
                <label for="qty">Qty.</label>
                <div class="input-group">
                    <select name="ref_type" id="ref_type" class="form-control" style="width: 45%;">
                        <option value="">Select</option>
                        <option value="1">Add</option>
                        <option value="-1">Minus</option>
                    </select>
                    <input type="text" name="qty" id="qty" class="form-control floatOnly" style="width: 55%;" value="">
                </div>
                <div class="error trans_type"></div>
            </div>
        </div>
    </div>
</form>