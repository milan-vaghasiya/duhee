<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
    <tbody>
        <tr class="in_process_id">
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Job No.</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($approvalData->job_card_id)) ? $approvalData->job_number : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Product</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($approvalData->product_code)) ? $approvalData->product_code : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Process</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($approvalData->in_process_name)) ? $approvalData->in_process_name : "" ?> ->
                <?= (!empty($approvalData->out_process_name)) ? $approvalData->out_process_name : "Store Location" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Pend. Qty.</th>
            <th class="text-left" id="pending_qty" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;"><?=(!empty($ref_id) || !empty($pending_qty))?$pending_qty:( (!empty($approvalData->ok_qty)) ? $approvalData->ok_qty - $approvalData->total_out_qty : "") ?></th>
        </tr>
    </tbody>
</table>
<form style="padding-top:35px;">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$ref_id?>">
            <input type="hidden" name="job_approval_id" id="job_approval_id" value="<?=$approvalData->id?>">
            <input type="hidden"  id="out_process_id" value="<?=$approvalData->out_process_id?>">
            <input type="hidden"  id="mfg_by" value="<?=$processData->mfg_by?>">
            <input type="hidden" id="pend_qty" name="pending_qty" value="<?=(!empty($ref_id) || !empty($pending_qty))?$pending_qty:( (!empty($approvalData->ok_qty)) ? $approvalData->ok_qty - $approvalData->total_out_qty : "") ?>">

            <div class="col-md-3 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=date("Y-m-d")?>">
            </div>

            <div class="col-md-3 form-group">
                <label for="send_to">Send To</label>
                <select name="send_to" id="send_to" class="form-control">
                    <option value="0" <?=($send_to == 0)?"selected":""?> <?=($processData->process_by == 2)?'disabled':''?>>In House</option>
                    <option value="1" <?=($send_to == 1)?"selected":""?> <?=($processData->process_by == 1)?'disabled':''?>>Vendor</option>
                    <option value="2" <?=($send_to == 2)?"selected":""?>>Store</option>
                </select>
            </div>

            <div class="col-md-3 form-group">
                <label for="handover_to">Handover To</label>
                <select name="handover_to" id="handover_to" class="form-control model-select2">
                    <?=$handover_to?>
                </select>
            </div>
            <div class="col-md-3 form-group root2Mc">
                <label for="root_2_mc">Machine</label>
                <select name="root_2_mc" id="root_2_mc" class="form-control single-select">
                    <option value="">Select</option>
                    <?php
                    if (!empty($mcList)) {
                        foreach ($mcList as $row) :
                            $machineName = (!empty($row->item_code) ? '[' . $row->item_code . '] ' : "") . $row->item_name;
                            echo '<option value="' . $row->id . '" >' . $machineName . '</option>';
                        endforeach;
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group">
                <label for="qty">Qty.</label>
                <input type="text" name="qty" id="qty" class="form-control floatOnly" value="">
            </div>
            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No</label>
                <select name="batch_no" id="batch_no" class="form-control single-select">
                    <?php $option='';
                        if(!empty($heatData)){
                            foreach($heatData as $row){
                                if($row->pend_qty > 0){ $option.= '<option value="'.$row->batch_no.'">'.$row->batch_no.' | Pend : '.floatval($row->pend_qty).'</option>'; }
                            }
                        }
                        if(!empty($option)){ echo $option; }else{ echo '<option value="">Batch not found!</option>'; }
                    ?>
                </select>
            </div>
            <div class="col-md-7 form-group remarkDiv">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>

            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-outline-success btn-save-other btn-block"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>        
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Movement :
        </h5>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width:5%;">#</th>
                        <th>Date</th>
                        <th>Send To</th>
                        <th>Handover To</th>
                        <th>Qty.</th>
                        <th>Remark</th>                        
                        <th class="text-center" style="width:10%;">Action</th>
                    </tr>
                </thead>
                <tbody id="movementTransData">
                    <?php
                        if(!empty($transHtml)):
                            echo $transHtml;
                        else:
                    ?>
                        <tr><td colspan="7" class="text-center">No Data Found.</td></tr>
                    <?php
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(".root2Mc").hide();   
</script>