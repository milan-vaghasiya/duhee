<form>
    <div class="row">
        <div class="col-md-4 form-group">
            <input type="hidden" name="id">
            <input type="hidden" id="ref_id" name="ref_id" value="<?= (!empty($dataRow->id) ? $dataRow->id : '') ?>">
            <input type="hidden" id="entry_type" name="entry_type" value="<?= (!empty($entry_type) ? $entry_type : 2) ?>">
            <input type="hidden" id="operation_type" name="operation_type" value="<?= (!empty($operation_type) ? $operation_type : 1) ?>">
            <input type="hidden" id="job_trans_id" name="job_trans_id" value="<?= (!empty($dataRow->job_trans_id) ? $dataRow->job_trans_id : '') ?>">
            <input type="hidden" id="job_card_id" name="job_card_id" value="<?= (!empty($dataRow->job_card_id) ? $dataRow->job_card_id : '') ?>">
            <input type="hidden" id="ref_type" name="ref_type" value="<?= (!empty($dataRow->entry_type) ? $dataRow->entry_type : '') ?>">

            <input type="hidden" id="process_id" value="<?= (!empty($dataRow->process_id) ? $dataRow->process_id : '') ?>">
            <input type="hidden" id="part_id" value="<?= (!empty($dataRow->product_id) ? $dataRow->product_id : '') ?>">
            <label for="qty">Rej Qty</label>
            <input type="text" id="qty" name="qty" class="form-control req numericOnly">
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_reason">Rejection Reason</label>
            <select id="rr_reason" name="rr_reason" class="form-control single-select req">
                <option value="">Select Reason</option>
                <?php
                foreach ($rejectionComments as $row) :
                    $code = (!empty($row->code)) ? '[' . $row->code . '] - ' : '';
                    echo '<option value="' . $row->id . '" data-code="' . $row->code . '" data-reason="' . $row->remark . '" >' . $code . $row->remark . '</option>';

                endforeach;
                ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="rej_type">Rejection Type</label>
            <select id="rej_type" name="rej_type" class="form-control req">
                <option value="">Select type</option>
                <option value="1">Machine</option>
                <option value="3">Operator</option>
                <option value="4">Short Qty</option>
                <option value="2">Raw Material</option>
            </select>
            <div class="error rej_type"></div>
        </div>
        <div class="col-md-4 form-group">
            <label for="rr_stage">Rejection Stage</label>
            <select id="rr_stage" name="rr_stage" class="form-control single-select req">
                <?php if (empty($dataRow->stage)) { ?> <option value="">Select Stage</option> <?php } else {
                                                                                                echo $dataRow->stage;
                                                                                            } ?>
            </select>
        </div>
        <div class="col-md-4 form-group controlPlanEnable" >
            <label for="dimension_range"> Dimension</label>
            <select id="dimension_range" name="dimension_range" class="form-control single-select">
                <option value="">Select Dimension</option>
            </select>
        </div>
        <div class="col-md-4 form-group ">
            <label for="rr_by">Rejection By <span class="text-danger">*</span></label>
            <select id="rr_by" name="rr_by" class="form-control single-select req">
                <option value="">Select</option>
            </select>
        </div>
        <div class="col-md-12 form-group remarkDiv">
            <label for="remark">Variance</label>
            <input type="text" id="remark" name="remark" class="form-control req" value="">
        </div>
    </div>
</form>

