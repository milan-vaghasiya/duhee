<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
    <tbody>
        <tr class="in_process_id">
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Job No.</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->job_card_id)) ? $dataRow->job_number : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Product</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->product_code)) ? $dataRow->product_code : "" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Process</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dataRow->in_process_name)) ? $dataRow->in_process_name : "" ?> ->
                <?= (!empty($dataRow->out_process_name)) ? $dataRow->out_process_name : "Store Location" ?>
            </th>
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Qty.</th>
            <th class="text-left" id="pending_qty" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;"><?= (!empty($dataRow->pqty)) ? $dataRow->pqty : "" ?></th>
        </tr>
    </tbody>
</table>
<form style="padding-top:35px;">
    <!-- Comman Hidden Field -->
    <div class="col-md-12">
        <input type="hidden" id="entry_type" name="entry_type" value="<?= (!empty($dataRow->entry_type)) ? $dataRow->entry_type : 0 ?>">
        <input type="hidden" id="trans_type" name="trans_type" value="<?= (!empty($dataRow->trans_type)) ? $dataRow->trans_type : 0 ?>">
        <input type="hidden" id="ref_id" name="ref_id" value="<?= (!empty($dataRow->ref_id)) ? $dataRow->ref_id : '' ?>">
        <input type="hidden" id="vendor_id" name="vendor_id" value="<?= (!empty($dataRow->vendor_id)) ? $dataRow->vendor_id : '' ?>">
        <input type="hidden" id="mfg_by" name="mfg_by" value="<?= (!empty($dataRow->mfg_by)) ? $dataRow->mfg_by : 0?>">
        <input type="hidden" id="job_card_id" name="job_card_id" value="<?= (!empty($dataRow->job_card_id)) ? $dataRow->job_card_id : "" ?>">
        <input type="hidden" name="product_id" id="product_id" value="<?= (!empty($dataRow->product_id)) ? $dataRow->product_id : "" ?>" />
        <input type="hidden" id="job_approval_id" name="job_approval_id" value="<?= (!empty($dataRow->id)) ? $dataRow->id : "" ?>">
        <input type="hidden" id="in_process_id" name="in_process_id" value="<?= (!empty($dataRow->in_process_id)) ? $dataRow->in_process_id : "0" ?>">
        <input type="hidden" id="out_process_id" name="out_process_id" value="<?= (!empty($dataRow->out_process_id)) ? $dataRow->out_process_id : "0" ?>">
        <input type="hidden" name="cycle_time" id="cycle_time" class="form-control floatOnly" value="<?= (!empty($cycle_time) ? $cycle_time : 0) ?>">
        <input type="hidden" name="load_unload_time" id="load_unload_time" class="form-control floatOnly" value="0">
    </div>
    <div class="col-md-12">

        <!-- Comman I/P From Logsheet and OK Movement Form -->
        <div class="row">
            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-2'?> form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?= $maxDate ?>" max="<?= $maxDate ?>">
            </div>
            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-2'?> form-group">
                <label for="production_qty">Production Qty</label>
                <input type="text" name="production_qty" id="production_qty" class="form-control numericOnly req qtyCal" value="">

            </div>
            <div class=" <?=($dataRow->trans_type == 2)?'col-md-4':'col-md-2'?> form-group">
                <label for="out_qty">Ok Qty</label>
                <input type="text" name="out_qty" id="out_qty" class="form-control numericOnly req " readonly value="">
                <div class="error batch_stock_error"></div>
            </div>
            <div class="col-md-2 form-group" <?=($dataRow->trans_type == 2)?'hidden':''?>>
                <label for="rej_qty">Rejection Qty</label>
                <input type="text" name="rej_qty" id="rej_qty" class="form-control floatOnly qtyCal">
            </div>
            <div class="col-md-2 form-group" <?=($dataRow->trans_type == 2)?'hidden':''?>>
                <label for="rw_qty">Rework Qty</label>
                <input type="text" name="rw_qty" id="rw_qty" class="form-control floatOnly qtyCal">
            </div>
            <div class="col-md-2 form-group" <?=($dataRow->trans_type == 2)?'hidden':''?>>
                <label for="hold_qty">Suspected Qty</label>
                <input type="text" name="hold_qty" id="hold_qty" class="form-control floatOnly qtyCal">
            </div>
        </div>
        
        <!-- <hr style="width:100%"> Logsheet I/P -->
        <div class="row" <?= ((!empty($masterOption->op_mc_shift)  && $masterOption->op_mc_shift == 2) || (!empty($dataRow->entry_type) && $dataRow->entry_type == 4) && $dataRow->mfg_by !=1) ? "hidden" : "" ?>>
            <div class="col-md-2 form-group">
    			<label for="start_time">Start Time</label>
    			<input type="time" name="start_time" id="start_time" class="form-control " value="">
    			<div class="error start_time"></div>
    		</div>
    		<div class="col-md-2 form-group">
    			<label for="end_time">End Time</label>
    			<input type="time" name="end_time" id="end_time" class="form-control " value="">
    			<div class="error end_time"></div>
    		</div>
		
            <div class="col-md-3 form-group">
                <label for="machine_id">Machine</label>
                <select name="machine_id" id="machine_id" class="form-control single-select asignOperator">
                    <option value="">Select Machine</option>
                    <option value="0" <?=(empty($machine->machine_id))?'selected':''?>>Department</option>
                    <?php
                    if (!empty($machineData)) {
                        foreach ($machineData as $row) :
                            $selected = (!empty($machine->machine_id) && $machine->machine_id ==$row->id)?'selected':'';
                            $machineName = (!empty($row->item_code) ? '[' . $row->item_code . '] ' : "") . $row->item_name;
                            echo '<option value="' . $row->id . '" '.$selected.'>' . $machineName . '</option>';
                        endforeach;
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-2 form-group" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 4 && $dataRow->mfg_by !=2)?'hidden':''?>>
                <label for="shift_id">Shift</label>
                <select name="shift_id" id="shift_id" class="form-control single-select11 asignOperator">
                    <option value="">Select Shift</option>
                    <?php
                    foreach ($shiftData as $row) :
                        $selected = (!empty($dataRow->shift_id) && $dataRow->shift_id == $row->id) ? "selected" : "";
                        $production_time = floatVal($row->production_hour) * 60;
                        echo '<option value="' . $row->id . '" ' . $selected . ' data-production_time="' . $production_time . '">' . $row->shift_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            <div class="col-md-3 form-group" <?=(!empty($dataRow->entry_type) && $dataRow->entry_type == 4 && $dataRow->mfg_by !=2)?'hidden':''?>>
                <label for="operator_id">Operator</label>
                <select name="operator_id" id="operator_id" class="form-control single-select">
                    <option value="">Select Operator</option>
                    <?php
                    foreach ($operatorList as $row) :
                        $selected = (!empty($dataRow->operator_id) && $dataRow->operator_id == $row->id) ? "selected" : "";
                        echo '<option value="' . $row->id . '" ' . $selected . '>[' . $row->emp_code . '] ' . $row->emp_name . '</option>';
                    endforeach;
                    ?>
                </select>
            </div>
            
        </div>
        <!--
        <hr>
        <div class="row" <?=($dataRow->trans_type == 2)?'hidden':''?>>
            <div class="col-md-4 form-group">
                <label for="rej_qty">Rejection Qty</label>
                <input type="text" name="rej_qty" id="rej_qty" class="form-control floatOnly qtyCal">
            </div>
            <div class="col-md-4 form-group">
                <label for="rw_qty">Rework Qty</label>
                <input type="text" name="rw_qty" id="rw_qty" class="form-control floatOnly qtyCal">
            </div>
            <div class="col-md-4 form-group">
                <label for="hold_qty">Suspected Qty</label>
                <input type="text" name="hold_qty" id="hold_qty" class="form-control floatOnly qtyCal">
            </div>
        </div>-->
        
        <hr>
		
		<div class="row form-group">
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary idle_btn btn-block" title="Click Me" data-bs-toggle="collapse" href="#idleTime" role="button" aria-expanded="false" aria-controls="idleTime"> Idle Time Details</button>
            </div>
            <div class="col-md-10">
                <hr>
            </div>
        </div>
		
		<section class="collapse multi-collapse d-none" id="idleTime">
            <div class="row">
                <div class="col-md-2 form-group">
                    <label for="idle_time">Idle Time (in minutes)</label>
                    <input type="text" name="idle_time" id="idle_time" class="form-control numericOnly" value="">
                </div>
                <div class="col-md-2 form-group">
                    <label for="idle_reason">Reason</label>
                    <select name="idle_reason" id="idle_reason" class="form-control">
                        <option value="">Select Reason</option>
                        <?php
                            foreach($idleReason as $row):
                                echo '<option value="'.$row->id.'">'.((!empty($row->code))?"[".$row->code."] ":"").$row->remark.'</option>';
                            endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label for="idle_remark">Remark</label>
                    <input type="text" name="idle_remark" id="idle_remark" class="form-control" value="">
                </div>
                <div class="col-md-2 form-group">
                    <label for="">&nbsp;</label>
                    <button type="button" id="addIdleRow" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> ADD</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 form-group">
                    <div class="table-responsive">
                        <table  id="idleTimeTable" class="table table-bordered">
                            <thead class="thead-info ">
                                <tr>
                                    <th>#</th>
                                    <th>Idle Time</th>
                                    <th>Reason</th>
                                    <th>Remark</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="idleTimeData">
                                <tr id="noData">
                                    <td class="text-center" colspan="5">No data available in table</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        
        <div class="row">  
            <?php
            $remarkWidth = "col-md-7";
            if((!empty($dataRow->entry_type) && $dataRow->entry_type == 4)){
                $remarkWidth = "col-md-5";
                ?>
                <div class="form-group col-md-2">
                    <label for="in_challan_no">In Challan No</label>
                    <input type="text" name="in_challan_no" id="in_challan_no" class="form-control">
                </div>
                <?php
            }
            ?>   
            <div class="col-md-3 form-group">
                <label for="batch_no">Batch No</label>
                <select name="batch_no" id="batch_no" class="form-control single-select">
                    <?php $option='';
                        if(!empty($heatData)){
                            foreach($heatData as $row){
                                if(abs($row->prev_pend_qty) > 0){ $option.= '<option value="'.$row->batch_no.'">'.$row->batch_no.' | Pend : '.floatval(abs($row->prev_pend_qty)).'</option>'; }
                            }
                        }
                        if(!empty($option)){ echo $option; }else{ echo '<option value="">Batch not found!</option>'; }
                    ?>
                </select>
            </div>       
            <div class="<?=$remarkWidth?> form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>
            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn waves-effect waves-light btn-success btn-block float-right save-form" onclick="saveOutward('outWard')" style="padding:5px 40px;"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>
        <div class="error general_error col-md-12"></div>
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Transaction :</h5>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered table-striped fs-12">
                <thead class="thead-info">
                    <tr>
                        <th style="width:30px;">#</th>
                        <th>Date</th>
                        <th>Cycle Time</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Idle Time(Min.)</th>
                        <!-- <th>Production Time</th>
                        <?php
                            // $clsp = 11;
                            // if (!empty($masterOption->op_mc_shift)  && $masterOption->op_mc_shift == 1) {
                            //     $clsp = 12;
                        ?>
                            <th>Machine</th>
                            <th>Operator</th>
                            <th>Shift</th>
                        <?php
                            // }
                        ?> -->
                        <?php
                        if(!empty($dataRow->entry_type) && $dataRow->entry_type == 4){
                            ?>
                            <th>In Challan No</th>
                            <?php
                        }else{
                            ?>
                            <th>Machine</th>
                            <th>Operator</th>
                            <th>Shift</th>
                            <?php
                        }
                        ?>
                        <th>Batch No</th>
                        <th>Out Qty.</th>
                        <th>Rej. Qty.</th>
                        <th>RW Qty.</th>
                        <th>Hold Qty.</th>
                        <th>Remark</th>
                        <th style="width:100px;">Action</th>
                    </tr>
                </thead>
                <tbody id="outwardTransData">
                    <?php
                        $html = "";
                        $i = 1;
                        if (!empty($outwardTrans)) :
                            
                            echo $outwardTrans;
                        else :
                    ?>
                        <td colspan="16" class="text-center">No Data Found.</td>
                    <?php
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>