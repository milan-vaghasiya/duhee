<form>
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12 form-group">
                <table class="table" style="border-radius:15px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);">
					<tr class="">
						<th class="text-center text-white" style="background:#aeaeae;width:10%;padding:0.25rem 0.5rem;border-top-left-radius:15px;border-bottom-left-radius:15px;border:0px;">Product Code</th>
						<th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?=$inspectionData['product_code']?></th>

                        <th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Product Name</th>
                        <th class="text-left" style="background:#f3f2f2;width:25%;padding:0.25rem 0.5rem;" id="ProductItemName"><?=$inspectionData['product_name']?></th>
						
						<th class="text-center text-white" style="background:#aeaeae;width:15%;padding:0.25rem 0.5rem;">Pending Qty.</th>
						<th class="text-left" style="background:#f3f2f2;width:15%;padding:0.25rem 0.5rem;border-top-right-radius:15px; border-bottom-right-radius:15px;border:0px;" id="ProductPendingQty"><?=$inspectionData['pending_qty']?></th>
					</tr>
				</table>
                <div id="hidden-input">
                    <input type="hidden" name="id" value="<?=$inspectionData['id']?>">
                    <input type="hidden" name="pending_qty" id="pending_qty" value="<?=$inspectionData['pending_qty']?>">
                    <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$inspectionData['job_card_id']?>">
                    <input type="hidden" name="product_id" id="product_id" value="<?=$inspectionData['product_id']?>">
                </div>
            </div>

            <div class="col-md-4 form-group">
                <label for="parameter_id">Parameter</label>
                <select id="parameter_id" class="form-control single-select req">
                    <option value="">Select Parameter</option>
                    <?php
                        foreach($inspectionParam as $row):
                            echo "<option value='".$row->id."'>".$row->parameter."</option>";
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="min_qty">Min Qty.</label>
                <input type="number" id="min_qty" class="form-control floatOnly" value="" />
            </div>

            <div class="col-md-2 form-group">
                <label for="max_qty">Max Qty.</label>
                <input type="number" id="max_qty" class="form-control floatOnly" value="" />
            </div>

            <div class="col-md-4 form-group">
                <label for="inspector_id">Inspector Name</label>
                <select id="inspector_id" class="form-control single-select req">
                    <option value="">Select Inspector</option>
                    <?php
                        foreach($inspectorData as $row):
                            echo "<option value='".$row->id."'>".$row->emp_name."</option>";
                        endforeach;
                    ?>
                </select>
            </div>

            <div class="col-md-2 form-group">
                <label for="ok_qty">OK Qty.</label>
                <input type="number" id="ok_qty" class="form-control floatOnly" min="0" value="0">
            </div>
            <div class="col-md-2 form-group">
                <label for="ud_qty">UD Qty.</label>
                <input type="number" id="ud_qty" class="form-control floatOnly" min="0" value="0">
            </div>
            <div class="col-md-2 form-group">
                <label for="rework_qty">Rework Qty.</label>
                <input type="number" id="rework_qty" class="form-control floatOnly" min="0" value="0">
            </div>
            <div class="col-md-2 form-group">
                <label for="mcr_qty">M/C. Rejection Qty.</label>
                <input type="number" id="mcr_qty" class="form-control floatOnly" min="0" value="0">
            </div>
            <div class="col-md-2 form-group">
                <label for="rmr_qty">RM Rejection Qty.</label>
                <input type="number" id="rmr_qty" class="form-control floatOnly" min="0" value="0">
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-outline-success waves-effect btn-block add-item"><i class="fa fa-plus"></i> Add</button>
            </div>
            <div class="col-md-12 form-group">
                <div class="error general_error"></div>
            </div>
        </div>
        
    </div>
    <hr>
    <div class="col-md-12 mt-10">
        <div class="table-responsive">
            <table id="inspectionTable" class="table table-bordered align-items-center" style="width: 100%;">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width: 5%;">#</th>
                        <th>Parameter</th>
                        <th>Min Qty.</th>
                        <th>Max Qty.</th>
                        <th>OK Qty.</th>
                        <th>UD Qty</th>
                        <th>Rework Qty.</th>
                        <th>M/C. Rej. Qty.</th>
                        <th>RM Rej. Qty.</th>
                        <th>Inspector Name</th>
                        <th class="text-center" style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="inspectionData">
                    <?php
                        if(!empty($inspectionTrans)):
                            $i=1;$totalQty=0;
                            foreach($inspectionTrans as $row):
                                $totalQty = $row->ok_qty + $row->ud_qty + $row->rework_qty + $row->mcr_qty + $row->rmr_qty;
                    ?>
                        <tr>
                            <td><?=$i++?></td>
                            <td>
                                <?=$row->parameter?>
                                <input type="hidden" name="parameter_id[]" value="<?=$row->parameter_id?>">
                                <input type="hidden" name="trans_id[]" value="<?=$row->id?>">
                            </td>
                            <td>
                                <?=$row->min_qty?>
                                <input type="hidden" name="min_qty[]" value="<?=$row->min_qty?>">
                            </td>
                            <td>
                                <?=$row->max_qty?>
                                <input type="hidden" name="max_qty[]" value="<?=$row->max_qty?>">
                            </td>
                            <td>
                                <?=$row->ok_qty?>
                                <input type="hidden" name="ok_qty[]" value="<?=$row->ok_qty?>">
                            </td>
                            <td>
                                <?=$row->ud_qty?>
                                <input type="hidden" name="ud_qty[]" value="<?=$row->ud_qty?>">
                            </td>
                            <td>
                                <?=$row->rework_qty?>
                                <input type="hidden" name="rework_qty[]" value="<?=$row->rework_qty?>">
                            </td>
                            <td>
                                <?=$row->mcr_qty?>
                                <input type="hidden" name="mcr_qty[]" value="<?=$row->mcr_qty?>">
                            </td>
                            <td>
                                <?=$row->rmr_qty?>
                                <input type="hidden" name="rmr_qty[]" value="<?=$row->rmr_qty?>">
                            </td>
                            <td>
                                <?=$row->inspector_name?>
                                <input type="hidden" name="inspector_id[]" value="<?=$row->inspector_id?>">
                            </td>
                            <td class="text-center">
                                <button type="button" onclick="Remove(this,<?=$totalQty?>);" style="margin-left:4px;" class="btn btn-outline-danger waves-effect waves-light"><i class="ti-trash"></i></button>
                            </td>
                        </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                    <tr id="noData">
                        <td class="text-center" colspan="11">No data available in table</td>
                    </tr>
                    <?php
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="col-md-12 form-group">
        <label for="remark">Remark</label>
        <input type="text" name="remark" id="remark" class="form-control" value="<?=$inspectionData['remark']?>">
    </div>
</form>