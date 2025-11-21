<div class="col-md-12">
    <h5 class="text-dark"><span id="itemName"></span></h5>
</div>
<hr>
<form>
    <div class="col-md-12 row">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="item_id" id="item_id" value="" />
        <input type="hidden" name="ref_type" value="-1" />
        <div class="col-md-3 form-group">
            <label for="location_id">Store Location</label>
            <select name="location_id" id="location_id" class="form-control model-select2 req">
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
        <div class="col-md-2 form-group">
            <label for="batch_no">Batch/Heat No.</label>
            <input type="text" name="batch_no" id="batch_no" class="form-control" value="" />
        </div>
        <?php if(!empty($itemData) AND $itemData->item_type==2) { ?>
            <div class="col-md-2 form-group">
                <label for="stock_type">Stock Type</label>
                <select name="stock_type" id="hsn_req" class="form-control">
                    <option value="FRESH" <?=(!empty($dataRow->stock_type) && $dataRow->stock_type == "FRESH")?"selected":""?>>FRESH</option>
                    <option value="USED" <?=(!empty($dataRow->stock_type) && $dataRow->stock_type == "USED")?"selected":""?>>USED</option>
                </select>
            </div>
        <?php } ?>
        <?php if(!empty($itemData) AND $itemData->item_type==3) { ?>
            <div class="col-md-3 form-group">
                <label for="ref_id">Supplier</label>
                <select name="ref_id" id="ref_id" class="form-control single-select">
                    <option value="">Select Supplier</option>
                    <?php
                        if(!empty($supplierData)):
                            foreach($supplierData as $row):
                                echo '<option value="'.$row->id.'>">'.$row->party_name.'</option>';
                            endforeach; 
                        endif;
                     ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="ref_batch">GRN No.</label>
                <input type="text" name="ref_batch" id="ref_batch" class="form-control" value="" />
            </div>
        <?php } ?>
        <div class="col-md-2 form-group">
            <label for="qty">Quantity</label>
            <input type="text" name="qty" id="qty" class="form-control floatOnly req" />           
        </div>
        <div class="col-md-12 form-group">
            <button type="button" class="btn waves-effect waves-light btn-outline-success mt-30 save-form float-right" onclick="saveOpening(this.form);"><i class="fa fa-plus"></i> Add Stock</button>
        </div>
    </div>
</form>

<hr>
<div class="col-md-12">
    <div class="table-responsive">
        <table id="openingStockTable" class="table table-bordered align-items-center">
            <thead class="thead-info">
                <tr>
                    <th style="width:5%;">#</th>
                    <th>Store Location</th>
                    <?php if(!empty($itemData) AND $itemData->item_type==3) { echo '<th>Supplier</th><th>GRN No.</th>';} ?>
                    <th>Batch No.</th>
                    <th>Qty</th>
                    <th class="text-center" style="width:10%;">Action</th>
                </tr>
            </thead>
            <tbody id="openingStockData">
                <?=$openingStockData['htmlData']?>
            </tbody>
        </table>
    </div>
</div>