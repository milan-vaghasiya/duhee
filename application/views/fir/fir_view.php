
<form id="firLotForm">
<div class="row">
    <!-- Column -->
    <div class="col-lg-12 col-xlg-12 col-md-12">
        <table class="table table-bordered-dark item-list-bb">
            <tr class="bg-light">
                <th>FIR No</th>
                <th>Date</th>
                <th>FG Batch No</th>
                <th>Product </th>
                <th>Job No </th>
                <th>Qty </th>
            </tr>
            <tr>
                <td><?= !empty($dataRow->fir_number) ? $dataRow->fir_number :'' ?></td>
                <td><?= !empty($dataRow->fir_date) ? $dataRow->fir_date :'' ?></td>
                <td><?= !empty($dataRow->fg_batch_no) ? $dataRow->fg_batch_no  :'' ?></td>
                <td><?= !empty($dataRow->full_name) ? $dataRow->full_name :'' ?></td>
                <td><?= !empty($dataRow->job_number) ? $dataRow->job_number :'' ?></td>
                <td><?= floatval(!empty($dataRow->qty) ? $dataRow->qty : '') ?></td>
            </tr>
        </table>
    </div>
    <div class="col-lg-12 col-xlg-12 col-md-12">
        <table class="table table-bordered-dark item-list-bb text-left">
            <tr >
                <th style="width:10%">Total Ok</th>
                <td><?= (!empty(floatval($dataRow->total_ok_qty))) ? floatval($dataRow->total_ok_qty) : 0 ?></td>
                <th  style="width:15%">Total Rejection</th>
                <td><?= (!empty(floatval($dataRow->total_rej_qty))) ? floatval($dataRow->total_rej_qty) : 0 ?> </td>
                <th  style="width:10%">Total Rework</th>
                <td><?= (!empty(floatval($dataRow->total_rw_qty))) ? floatval($dataRow->total_rw_qty) : 0 ?></td>
            </tr>
        </table>
    </div>
    <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
    <div class="col-md-4 form-group">
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
    <div class="col-lg-12 col-xlg-12 col-md-12">
        <div class="table-responsive">
        <table class="table table-bordered-dark item-list-bb">
            <thead>
                    <tr class="text-center bg-light">
                        <th style="width:3%;">#</th>
                        <th class="text-left">Special Char.</th>
                        <th class="text-left">Product Parameter</th>
                        <th>Product Specification</th>
                        <th>Instrument</th>
                        <th>Sample Freq.</th>
                        <th>Date</th>
                        <th>OK</th>
                        <th>UD OK</th>
                        <th>Rejection</th>
                        <th>Remark</th>
                        <th>Rework</th>
                        <th>Inspected By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($firDimensionData)) :
                        $i = 1;
                        foreach ($firDimensionData as $row) :
                            $diamention = '';
                            if ($row->requirement == 1) { $diamention = $row->min_req . '/' .  $row->max_req; }
                            if ($row->requirement == 2) { $diamention = $row->min_req . ' ' .  $row->other_req; }
                            if ($row->requirement == 3) { $diamention = $row->max_req . ' ' .  $row->other_req; }
                            if ($row->requirement == 4) { $diamention = $row->other_req; }
                    ?>
                            <tr class="text-center">
                                <td><?= $i ?></td>
                                <td class="text-left"><?php if (!empty($row->char_class)) { ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/' . $row->char_class . '.png') ?>"><?php } ?></td>
                                <td><?= $row->parameter ?></td>
                                <td><?= $diamention ?></td>
                                <td><?= $row->instrument_code ?></td>
                                <td><?= $row->potential_cause ?></td>
                                <td>  <?= !empty($row->trans_date) ? ($row->trans_date) :  "" ?> </td>
                                <td><?= !empty($row->ok_qty) ? floatval($row->ok_qty) : '' ?> </td>
                                <td><?= !empty($row->ud_ok_qty) ? floatval($row->ud_ok_qty) : '' ?></td>
                                <td><?= !empty($row->rej_qty) ? floatval($row->rej_qty) : '' ?></td>
                                <td><?= !empty($row->remark) ? floatval($row->remark) : '' ?></td>
                                <td><?= !empty($row->rw_qty) ? floatval($row->rw_qty) : '' ?></td>
                                <td><?= !empty($row->emp_name) ?$row->emp_name : '' ?> </td>
                            </tr>

                        <?php $i++; endforeach;
                    else : ?>
                        <tr>
                            <td colspan="12" class="text-center">No data available in table </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</form>
							