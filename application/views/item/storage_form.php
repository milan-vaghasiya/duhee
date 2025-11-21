<form enctype="multpart/form-data">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : ""; ?>" />
            <input type="hidden" name="item_type" value="<?= (!empty($dataRow->item_type)) ? $dataRow->item_type : ""; ?>" />
            <div class="col-md-4 form-group">
                <label for="batch_stock">Stock Type</label>
                <select name="batch_stock" id="batch_stock" class="form-control">
                    <option value="0" <?= (!empty($dataRow->batch_stock) && $dataRow->batch_stock == 0) ? "selected" : "" ?>>None</option>
                    <option value="1" <?= (!empty($dataRow->batch_stock) && $dataRow->batch_stock == 1) ? "selected" : "" ?>>Batchwise</option>
                    <option value="2" <?= (!empty($dataRow->batch_stock) && $dataRow->batch_stock == 2) ? "selected" : "" ?>>Serial No Stock</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="item_status">Movement Type</label>
                <select name="movement_type" id="movement_type" class="form-control">
                    <option value="1" <?= (!empty($dataRow->movement_type) && $dataRow->movement_type == 1) ? "selected" : "" ?>>Fast Moving</option>
                    <option value="2" <?= (!empty($dataRow->movement_type) && $dataRow->movement_type == 2) ? "selected" : "" ?>>Slow Moving</option>
                    <option value="3" <?= (!empty($dataRow->movement_type) && $dataRow->movement_type == 3) ? "selected" : "" ?>>Medium Moving</option>
                    <option value="4" <?= (!empty($dataRow->movement_type) && $dataRow->movement_type == 4) ? "selected" : "" ?>>Non Moving</option>
                </select>
            </div>
            <div class="col-md-4 form-group lc">
                <label for="location">Store Location</label>
                <select id="location" name="location" class="form-control single-select1 model-select2 req">
                    <option value="" data-store_name="">Select Location</option>
                    <?php
                    foreach ($locationData as $lData) :
                        echo '<optgroup label="' . $lData['store_name'] . '">';
                        foreach ($lData['location'] as $row) :
                            $selected = (!empty($dataRow->location) && $dataRow->location == $row->id) ? "selected" : "";
                            echo '<option value="' . $row->id . '" data-store_name="' . $lData['store_name'] . '" ' . $selected . '>' . $row->location . ' </option>';
                        endforeach;
                        echo '</optgroup>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="wkg">Weight in Kg </label>
                <input type="text" name="wkg" class="form-control floatOnly" value="<?= (!empty($dataRow->wkg)) ? $dataRow->wkg : "" ?>" />
            </div>
            <div class="col-md-4 form-group">
                <label for="reorder_qty">Reorder Qty</label>
                <input type="text" name="reorder_qty" class="form-control floatOnly" value="<?= (!empty($dataRow->reorder_qty)) ? $dataRow->reorder_qty : "" ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="lead_time">Lead Time (Days)</label>
                <input type="text" name="lead_time" class="form-control floatOnly" value="<?= (!empty($dataRow->lead_time)) ? $dataRow->lead_time : "" ?>" />
            </div>


            <div class="col-md-4 form-group">
                <label for="min_qty">Stock Qty(%) </label>
                <div class="input-group">
                    <input type="text" name="min_qty" class="form-control floatOnly" placeholder="Min Stock" value="<?= (!empty($dataRow->min_qty)) ? $dataRow->min_qty : "" ?>" />
                    <input type="text" name="max_qty" class="form-control floatOnly" placeholder="Max Stock" value="<?= (!empty($dataRow->max_qty)) ? $dataRow->max_qty : "" ?>" />
                </div>
            </div>

            <div class="col-md-4 form-group">
                <label for="min_order_qty">Order Qty(%) </label>
                <div class="input-group">
                    <input type="text" name="min_order_qty" class="form-control floatOnly" placeholder="Min Order" value="<?= (!empty($dataRow->min_order_qty)) ? $dataRow->min_order_qty : "" ?>" />
                    <input type="text" name="max_order_qty" class="form-control floatOnly" placeholder="Max Order" value="<?= (!empty($dataRow->max_order_qty)) ? $dataRow->max_order_qty : "" ?>" />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="min_tqty_per">Tolerance Qty(%) </label>
                <div class="input-group">
                    <input type="text" name="min_tqty_per" class="form-control floatOnly" placeholder="Min %" value="<?= (!empty($dataRow->min_tqty_per)) ? $dataRow->min_tqty_per : "" ?>" />
                    <input type="text" name="max_tqty_per" class="form-control floatOnly" placeholder="Max %" value="<?= (!empty($dataRow->max_tqty_per)) ? $dataRow->max_tqty_per : "" ?>" />
                </div>
            </div>
            <div class="col-md-4 form-group">
                <label for="warranty_period">Warranty Period(Month)</label>
                <input type="text" name="warranty_period" class="form-control numericOnly " value="<?= (!empty($dataRow->warranty_period)) ? $dataRow->warranty_period : "" ?>" />
            </div>

            <div class="col-md-4 form-group">
                <label for="self_life">Self Life(Month)</label>
                <input type="text" name="self_life" class="form-control numericOnly" value="<?= (!empty($dataRow->self_life)) ? $dataRow->self_life : "" ?>" />
            </div>
        </div>
    </div>
</form>