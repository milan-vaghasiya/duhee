<form id="setupReq">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?= $approvalData->job_card_id ?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?= $approvalData->id ?>">
            <input type="hidden" name="process_id" id="process_id" value="<?= $approvalData->in_process_id ?>" />
            <input type="hidden" name="product_id" id="product_id" value="<?= $approvalData->product_id ?>">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="setup_type" id="setup_type" value="1">
            <div class="col-md-6 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select">
                    <option value="">Select Machine</option>
                    <?php
                    if (!empty($machineList)) {
                        foreach ($machineList as $row) {
                            $selected = (!empty($machine->machine_id) && $machine->machine_id == $row->id) ? 'selected' : '';
                    ?><option value="<?= $row->id ?>" <?= $selected ?>><?= (!empty($row->item_code) ? '[' . $row->item_code . '] ' : '') . $row->item_name ?></option><?php
                                                                                                                                                            }
                                                                                                                                                        }
                                                                                                                                                                ?>
                </select>
                <div class="error machine_id"></div>
            </div>
            <div class="col-md-6 form-group">
                <label for="setter_id">Setter</label>
                <select name="setter_id" id="setter_id" class="form-control single-select">
                    <option value="">Select Setter</option>
                    <?php
                    if (!empty($setterList)) {
                        foreach ($setterList as $row) {
                    ?><option value="<?= $row->id ?>"><?= $row->emp_name ?></option><?php
                                                                                    }
                                                                                }
                                                                                        ?>
                </select>
                <div class="error setter_id"></div>
            </div>
            <div class="col-md-4 form-group">
                <label for="qci_id">Inspector</label>
                <select name="qci_id" id="qci_id" class="form-control single-select">
                    <option value="">Select Inspector</option>
                    <?php
                    if (!empty($inspectorList)) {
                        foreach ($inspectorList as $row) {
                    ?>
                            <option value="<?= $row->id ?>"><?= $row->emp_name ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>
                <div class="error qci_id"></div>
            </div>
            <div class="col-md-8 form-group">
              
                <label for="remark">Remark</label>
                <div class="input-group">
                    <input type="text" name="remark" id="remark" class="form-control" >
                    <div class="input-append">
                        <?php $disabled = ($approvalData->status == 0 || $approvalData->status == 1) ? '' : 'disabled'; ?>
                        <button type="button" class="btn btn-success " <?= $disabled ?> id="setupSaveBtn" onclick="saveSetupReq('setupReq','setupRequestSave')"><i class="fa fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
</form>
<div class="row">
    <div class="col-md-12">
        <table class="table jp-table " id="setupReqTable">
            <thead class="lightbg">
                <tr>
                    <th>#</th>
                    <th>Req Date</th>
                    <th>Req No</th>
                    <th>Req By</th>
                    <th>Machine</th>
                    <th>Qc Inspector</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="setupReqTbody">
                <?= (!empty($htmlData)) ? $htmlData : '' ?>
            </tbody>
        </table>
    </div>

</div>