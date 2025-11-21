<table class="table" style="border-radius:0px;box-shadow: 1px 2px 2px 0 rgb(0 0 0 / 70%);left:0;top:0px;position:absolute;">
    <tbody>
        <tr class="in_process_id">
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;border-top-left-radius:0px;border-bottom-left-radius:0px;border:0px;">Job No.</th>
            <th class="text-left" style="background:#f3f2f2;padding:0.25rem 0.5rem;">
                <?= (!empty($dimensionData->job_card_id)) ? $dimensionData->fir_number : "" ?>
            </th>
            
            <th class="text-center text-white" style="background:#aeaeae;padding:0.25rem 0.5rem;">Pend. Qty.</th>
            <th class="text-left" id="pending_qty" style="background:#f3f2f2;padding:0.25rem 0.5rem;border-top-right-radius:0px; border-bottom-right-radius:0px;border:0px;"><?= (!empty($dimensionData->in_qty)) ? $dimensionData->in_qty - $dimensionData->ok_qty- $dimensionData->ud_ok_qty- $dimensionData->rw_qty-$dimensionData->total_rej_qty: "" ?></th>
        </tr>
    </tbody>
</table>
<form style="padding-top:35px;" id="fir_form">
    <div class="col-md-12">
        <div class="row">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="fir_id" id="fir_id" value="<?=$dimensionData->fir_id?>">
            <input type="hidden" name="ref_id" id="ref_id" value="<?=$dimensionData->id?>">
            <input type="hidden" name="job_card_id" id="job_card_id" value="<?=$dimensionData->job_card_id?>">
            <input type="hidden" name="in_qty" id="in_qty" value="<?=$dimensionData->in_qty?>">
            <input type="hidden" id="pend_qty" value="<?= (!empty($dimensionData->in_qty)) ? $dimensionData->in_qty - $dimensionData->ok_qty- $dimensionData->ud_ok_qty- $dimensionData->rw_qty-$dimensionData->total_rej_qty: "" ?>">

            <div class="col-md-2 form-group">
                <label for="entry_date">Date</label>
                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?=date("Y-m-d")?>">
            </div>

            <div class="col-md-2 form-group">
                <label for="ok_qty">OK Qty</label>
                <input type="text" name="ok_qty" id="ok_qty" class="form-control floatOnly" >
            </div>
            <div class="col-md-2 form-group">
                <label for="ud_ok_qty">UD OK Qty</label>
                <input type="text" name="ud_ok_qty" id="ud_ok_qty" class="form-control floatOnly" >
            </div>
            <div class="col-md-2 form-group">
                <label for="mc_rej_qty">Machine Rej Qty</label>
                <input type="text" name="mc_rej_qty" id="mc_rej_qty" class="form-control floatOnly" >
            </div>
            <div class="col-md-2 form-group">
                <label for="rm_rej_qty">RM Rej Qty</label>
                <input type="text" name="rm_rej_qty" id="rm_rej_qty" class="form-control floatOnly" >
            </div>
            <div class="col-md-2 form-group">
                <label for="rw_qty">RW Qty</label>
                <input type="text" name="rw_qty" id="rw_qty" class="form-control floatOnly" >
            </div>
            <div class="col-md-3 form-group">
                <label for="inspector_id">Inspected By</label>
                <select name="inspector_id" id="inspector_id" class="form-control single-select">
                    <option value="">Select Inspector</option>
                    <?php
                        if(!empty($empData)){
                            foreach($empData as $row){
                                ?>
                                <option value="<?=$row->id?>"><?=$row->emp_name?></option>
                                <?php
                            }
                        }
                    ?>
                </select>
            </div>
            <div class="col-md-7 form-group">
                <label for="remark">Remark</label>
                <input type="text" name="remark" id="remark" class="form-control" value="">
            </div>

            <div class="col-md-2 form-group">
                <label for="">&nbsp;</label>
                <button type="button" class="btn btn-outline-success btn-save-other btn-block" onclick="saveFIRTrans('fir_form')"><i class="fa fa-check"></i> Save</button>
            </div>
        </div>        
    </div>
</form>
<div class="col-md-12">
    <div class="row">
        <h5 style="width:100%;margin:0 auto;vertical-align:middle;border-top:1px solid #ccc;padding:5px 0px;">Process Movement :
        </h5>
        <div class="table-responsive">
            <table id='outwardTransTable' class="table table-bordered jpExcelTable">
                <thead class="thead-info">
                    <tr>
                        <th class="text-center" style="width:5%;">#</th>
                        <th>Date</th>
                        <th>OK Qty</th>
                        <th>UD OK Qty</th>
                        <th>Machine Rej Qty</th>
                        <th>RM Rej Qty</th>
                        <th>RW Qty</th>            
                        <th>Inspected By</th>            
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
                        <tr><td colspan="8" class="text-center">No Data Found.</td></tr>
                    <?php
                        endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>