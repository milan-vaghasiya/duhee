<form id="vendorChallanForm">
    <input type="hidden" name="vendor_id" id="vendor_id" value="<?= $vendor_id ?>" />
    <input type="hidden" name="challan_id" id="challan_id" value="0" />
    <div class="row">
        <div class="col-md-3">
            <label for="trans_date">Date</label>
            <input type="date" name="trans_date" id="trans_date" class="form-control float-right req" value="<?= date('Y-m-d') ?>">
        </div>
         <div class="col-md-3 form-group">
            <label for="desc_goods">Description Of Goods</label>
            <select name="desc_goods" id="desc_goods" class="form-control" >
                <option value="0">Select</option>
                <option value="Alloy Steel Forging" <?=(!empty($dataRow->desc_goods) && $dataRow->desc_goods == "Alloy Steel Forging")?"selected":""?>>Alloy Steel Forging</option>
                <option value="Steel Bars" <?=(!empty($dataRow->desc_goods) && $dataRow->desc_goods == "Steel Bars")?"selected":""?>>Steel Bars</option>
                <option value="CNC Finished" <?=(!empty($dataRow->desc_goods) && $dataRow->desc_goods == "CNC Finished")?"selected":""?>>CNC Finished</option>
            </select>
        </div>
        <div class="col-md-3 form-group">
            <label for="sap_no">SAP No.</label>
            <input type="text" name="sap_no" id="sap_no" class="form-control" value="">
        </div> 
        <div class="col-md-3 form-group">
            <label for="hsn_code">Hsn Code</label>
            <input type="text" name="hsn_code" id="hsn_code" class="form-control" value="">
        </div> 
        <div class="col-md-3 form-group">
            <label for="nature_process">Nature Of Process</label>
            <input type="text" name="nature_process" id="nature_process" class="form-control" value="">
        </div>
        <div class="col-md-9 form-group">
            <label for="remark">Remark</label>
            <input type="text" name="remark" id="remark" class="form-control" value="">
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="error orderError"></div><br>
                <table id='outsourceTransTable' class="table table-bordered jpDataTable colSearch">
                    <thead class="thead-info">
                        <tr class="text-center">
                            <th class="text-center" style="width:5%;">#</th>
                            <th class="text-center" style="width:12%;">Job No.</th>
                            <th class="text-center" style="width:10%;">Job Date</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Process</th>
                            <th class="text-center" style="width:8%;">Ok Qty.</th>
                            <th class="text-center" style="width:10%;">Pending Qty.</th>
                            <th>Challan Qty.</th>
                            <th>Price</th>
							<th>GST(%)</th>
                        </tr>
                    </thead>
                    <tbody id="outsourceTransData">
                        <?php
                        if (!empty($resultData)) {
                            $i=0;
                            foreach ($resultData as $row) {
                                
                                echo '<tr>
                                    <td class="text-center fs-12">
                                        <input type="checkbox" id="md_checkbox_' . $i . '" name="id[]" class="filled-in chk-col-success challanCheck" data-rowid="' . $i . '" value="' . $row->id . '"  ><label for="md_checkbox_' . $i . '" class="mr-3"></label>
                                    </td>
                                    <td class="text-center fs-12">' . $row->job_number . '</td>
                                    <td class="text-cente fs-12">' . formatDate($row->job_date) . '</td>
                                    <td class="text-center fs-12">' . $row->full_name . '</td>
                                    <td class="text-center fs-12">' . $row->process_name . '</td>
                                    <td class="text-center fs-12">' . floatVal($row->qty) . '</td>
                                    <td class="text-center fs-12">' . $row->pending_qty . '</td>
                                    <td class="text-center fs-12">
                                        <input type="hidden" id="out_qty' . $i . '" value="' . floatVal($row->pending_qty) . '">                   
                                        <input type="text" id="ch_qty' . $i . '" name="ch_qty[]" data-rowid="' . $i . '" class="form-control challanQty floatOnly" value="0" disabled>
                                        <input type="hidden" id="mfg_by' . $i . '" name="mfg_by[]" value="'.$row->mfg_by.'" data-rowid="' . $i . '"  disabled>
                                        <div class="error chQty' . $row->id . '"></div>
                                    </td>
    								<td class="text-center fs-12">
    									<input type="text" id="price' . $i . '" name="price[]" value="' . $row->price . '" class="form-control floatOnly" disabled>
    								</td>
    								<td class="text-center fs-12">
    									<select name="gst_per['.$i.']" id="gst_per'.$i.'" class="form-control" disabled>';
    										foreach($gstPercentage as $rowData):
    											echo '<option value="'.$rowData['rate'].'">'.$rowData['val'].'</option>';
    										endforeach;
    								echo '</select>	
    									</td>
    								</tr>';
                                $i++;
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="7" class="text-center">No data available in table</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

</form>