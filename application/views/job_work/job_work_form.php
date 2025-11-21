<form>
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="<?=(!empty($dataRow->id))?$dataRow->id:''?>">
            <div class="col-md-3 form-group">
                <label for="trans_no">Jobwork No</label>
                <input type="hidden" name="trans_no" id="trans_no" value="<?=(!empty($dataRow->trans_no))?$dataRow->trans_no:$jobworkNo?>">
                <input type="hidden" name="trans_prefix" id="trans_prefix" value="<?=(!empty($dataRow->trans_prefix))?$dataRow->trans_prefix:$jobworkPrefix?>">
                <input type="text" name="trans_number" id="trans_number" class="form-control req" value="<?=(!empty($dataRow->trans_prefix) && !empty($dataRow->trans_no))?$dataRow->trans_prefix.$dataRow->trans_no:$jobworkPrefix.$jobworkNo?>" readonly>
            </div>
            <div class="col-md-3 form-group">
                <label for="trans_date">Date</label>
                <input type="date" name="trans_date" id="trans_date" class="form-control req" value="<?= date("Y-m-d") ?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="vendor_id">Vendor</label>
                <select name="vendor_id" id="vendor_id" class="form-control single-select">
                    <option value="">Select Vendor</option>
                    <?php
                        if (!empty($vendorList)) {
                            foreach ($vendorList as $row) {
                                $selected = (!empty($dataRow->vendor_id) && $dataRow->vendor_id == $row->id)?'selected':'';
                                echo '<option value="'.$row->id.'" '.$selected.'>'.$row->party_name.'</option>';
                            }
                        }
                    ?>
                </select>
                <input type="hidden" name="vendor_name" id="vendor_name" value="<?=(!empty($dataRow->party_name))?$dataRow->party_name:''?>">
            </div>
            <div class="col-md-3 form-group">
                <label for="vehicle_no">Vehicle No.</label>
                <select name="vehicle_no" id="vehicle_no" class="form-control single-select">
					<option value="">Select Vehicle</option>
                    <?php
						foreach ($vehicleData as $vehicle_no) :
							$selected = (!empty($dataRow->vehicle_no) && $dataRow->vehicle_no == $vehicle_no) ? "selected" : "";
							echo '<option value="' . $vehicle_no . '" ' . $selected . '>' . $vehicle_no . '</option>';
						endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="ewb_no">E-Way Bill No.</label>
                <input type="text" name="ewb_no" id="ewb_no" class="form-control" value="<?=(!empty($dataRow->ewb_no))?$dataRow->ewb_no:''?>">
            </div>
            <div class="col-md-9 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="<?=(!empty($dataRow->remark))?$dataRow->remark:''?>">
            </div>
        </div>
        <hr width="100%">
        <div class="row">
            <div class="col-md-8 form-group">
                <label for="job_order_trans_id">Item</label>
                <select name="job_order_trans_id" id="job_order_trans_id" class="form-control single-select">
                    <option value="">Select Item</option>
                    <?php
                        if (!empty($productList)) {
                            foreach ($productList as $row) {
                                echo '<option value="'.$row->id.'" data-item_id="'.$row->id.'" data-item_name="'.$row->full_name.'"  data-price="'.$row->price.'"  data-unit_name="'.$row->unit_name.'"  data-unit_id="'.$row->unit_id.'" >'.$row->full_name.'</option>';
                            }
                        }
                    ?>
                </select>
                <input type="hidden" name="trans_ref_id" id="trans_ref_id" value="">
            </div>
            <!--<div class="col-md-3 form-group">-->
            <!--    <label for="location_id">Location</label>-->
            <!--    <select name="location_id" id="location_id" class="form-control single-select req">-->
            <!--        <option value="">Select Location</option>-->
            <!--    </select>-->
            <!--</div>-->
            <!--<div class="col-md-3 form-group">-->
            <!--    <label for="batch_no">Batch No.</label>-->
            <!--    <select name="batch_no" id="batch_no" class="form-control single-select req">-->
            <!--        <option value="">Select Batch</option>-->
            <!--    </select>-->
            <!--</div>-->
            <div class="col-md-4 form-group">
                <label for="qty">Qty <small id="qtyLabel"></small></label>
                <input type="text" name="qty" id="qty" class="form-control" value="">
            </div>
            <div class="col-md-10 form-group">
                <label for="trans_remark">Remark</label>
                <input type="text" name="trans_remark" id="trans_remark" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-primary btn-block addRow"><i class="fas fa-plus"></i> Add</button>
            </div>
            <div class="col-md-12">
                <div class="error generalError"></div>
                <table id="jobworkItems" class="table jp-table text-center">
                    <thead class="lightbg">
                        <tr>
                            <th>Item</th>
                            <th>Process</th>
                            <th>Qty</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="tempItem">
                        <?php
                            if(!empty($dataRow->itemData)):
                                foreach($dataRow->itemData as $row):
                        ?>
                                    <tr>
                                        <td>
                                            <?=$row->full_name?>
                                            <input type="hidden" name="item_id[]" value="<?=$row->item_id?>">
                                            <input type="hidden" name="location_id[]" value="<?=$row->location_id?>">
                                            <input type="hidden" name="batch_no[]" value="<?=$row->batch_no?>">
                                            <input type="hidden" name="trans_id[]" value="<?=$row->id?>">
                                            <input type="hidden" name="job_order_trans_id[]" value="<?=$row->job_order_trans_id?>">
                                            <input type="hidden" name="trans_remark[]" value="<?=$row->remark?>">
                                            <input type="hidden" name="price[]" value="<?=$row->price?>">
                                        </td>
                                        <td>
                                            <?=$row->process_name?>
                                            <input type="hidden" name="process_id[]" value="<?=$row->process_id?>">
                                        </td>
                                        <td>
                                            <?=$row->qty?>
                                            <input type="hidden" name="qty[]" value="<?=$row->qty?>">
                                        </td>
                                        <?php if($row->received_qty <= 0){?>
                                            <td class="text-center" style="width:10%;">
                                                <?php $row = json_encode($row); ?>
                                                <button type="button" onclick="Remove(this);" class="btn btn-outline-danger waves-effect waves-light m-l-2"><i class="ti-trash"></i></button>
                                            </td>
                                        <?php } ?>
                                    </tr>
                        <?php
                                endforeach;
                            else:
                        ?>
                            <tr id="noData">
                                <td colspan="4" class="text-center">No data available in table</td>
                            </tr>
                        <?php
                            endif;
                        ?>
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#vehicle_no").attr("autocomplete", "off");
        $('#vehicle_no').typeahead({
			source: function(query, result) {
				$.ajax({
					url: base_url + controller + '/vehicleSearch',
					method: "POST",
					global: false,
					data: {
						query: query
					},
					dataType: "json",
					success: function(data) {
						result($.map(data, function(item) {
							return item;
						}));
					}
				});
			}
		});
    });
</script>