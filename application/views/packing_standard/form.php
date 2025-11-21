<div class="col-md-12">
    <form>
        <div class="row">
            <div class="col-md-12">
                <div class="error gerenal_error"></div>
            </div>
            <input type="hidden" name="id" id="id" value="" />
            <input type="hidden" name="item_id" id="item_id" value="" />
            <div class="col-md-4 form-group">
                <label for="wt_pcs">Weight Per Pcs.(K.G.)</label>
                <input type="text" name="wt_pcs" id="wt_pcs" class="form-control floatOnly req" value="" min="0" />
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 form-group">
                <label for="box_type">Material Type</label>
                <select name="box_type" id="box_type" class="form-control single-select">
                    <option value="0">Box</option>
                    <option value="1">Pallet</option>
                </select>
            </div>
            <div class="col-md-4 form-group">
                <label for="box_id">Packing Material</label>
                <select name="box_id" id="box_id" class="form-control single-select req">
                    <option value="">Select Packing Material</option>
                    <?php
                        foreach($boxData as $row):
                            echo '<option value="'.$row->id.'" data-unit_id="'.$row->unit_id.'">'.$row->item_name.'</option>';
                        endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="qty_per_box">Qty Per Box</label>
                <input type="text" name="qty_per_box" id="qty_per_box" class="form-control floatOnly req" value="" min="0" />
            </div>
            <div class="col-md-3 form-group">
                <label for="wt_per_box">Packing Weight(K.G.)</label>
                <input type="text" name="wt_per_box" id="wt_per_box" class="form-control floatOnly req" value="" min="0" />
            </div>
            <div class="col-md-1 form-group">
                <button type="button" class="btn btn-outline-success waves-effect waves-light mt-30 save-form" onclick="savePackingStandard('updatePackingStandard','savePackingStandard');" >Save</button>
            </div>
        </div>
    </form>
    <hr>
    <div class="row">
        <div class="table-responsive">
            <table id="packingStandard" class="table table-bordered align-items-center">
                <thead class="thead-info">
                    <tr>
                        <th style="width:5%;">#</th>
                        <th>Material Type</th>
                        <th>Packing Material</th>
                        <th>Qty Per Box</th>
                        <th>Packing Weight(K.G.)</th>
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="stadardBody">
                    <?php echo $standardData; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>