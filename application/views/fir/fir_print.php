<form id="firLotForm">
    <div class="row">
        <!-- Column -->
        <table class="table item-list-bb text-left" style="margin-top:2px;">
            <tr>
                <th>Part Description:-</th>
                <td ><?= !empty($dataRow->full_name) ? $dataRow->full_name : '' ?></td>
                <th> Code :-</th>
                <td><?= !empty($dataRow->item_code) ? $dataRow->item_code : '' ?></td>
                <th>FIR Qty. :-</th>
                <td><?= floatval(!empty($dataRow->qty) ? $dataRow->qty : '') ?></td>
                
            </tr>
            <tr>
                <th>Part No.:-</th>
                <td><?= !empty($dataRow->part_no) ? $dataRow->part_no : '' ?></td>
                <th>FIR Date</th>
                <td><?= !empty($dataRow->fir_date) ? formatDate($dataRow->fir_date) : '' ?></td>
                <th>Jobcard No </th>
                <td><?= !empty($dataRow->job_number) ? $dataRow->job_number : '' ?></td>
            </tr>
            <tr>
                <th>Latest Rev. Change Level.:-</th>
                <td ><?= !empty($pdiData->rev_no) ? $pdiData->rev_no : '' ?></td>
                <th>FIR No.:-</th>
                <td><?= !empty($dataRow->fir_number) ? $dataRow->fir_number : '' ?></td>
                <th>Lot No.:-</th>
                <td><?= !empty($dataRow->fir_no) ? $dataRow->fir_no : '' ?></td>
            </tr>
            
        </table>
        
        <!-- <div class="col-lg-12 col-xlg-12 col-md-12">
            <table class="table table-bordered-dark item-list-bb text-left">
                <tr>
                    <th style="width:10%">Total Ok</th>
                    <td></td>
                    <th style="width:15%">Total Rejection</th>
                    <td><?= (!empty(floatval($dataRow->total_rej_qty))) ? floatval($dataRow->total_rej_qty) : 0 ?> </td>
                    <th style="width:10%">Total Rework</th>
                    <td><?= (!empty(floatval($dataRow->total_rw_qty))) ? floatval($dataRow->total_rw_qty) : 0 ?></td>
                </tr>
            </table>
        </div> -->
        <input type="hidden" id="id" name="id" value="<?= !empty($dataRow->id) ? $dataRow->id : '' ?>">
        <div class="col-lg-12 col-xlg-12 col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered-dark item-list-bb " style="margin-top: 20px;">
                    <thead>
                        <tr class="text-center bg-light">
                            <th style="width:3%;" rowspan="3">#</th>
                            <th class="text-left"  rowspan="3">Special Char.</th>
                            <th class="text-left"  rowspan="3">Product Parameter</th>
                            <th  rowspan="3">Product Specification</th>
                            <th  rowspan="3">Instrument</th>
                            <th rowspan="3">Sample Freq.</th>
                            <th colspan="6">Observation</th>
                        </tr>
                        <tr class="text-center bg-light">
                            <th rowspan="2">OK</th>
                            <th  rowspan="2">Under Deviation</th>
                            <th colspan="2">Rejection</th>
                            <th  rowspan="2">Rework</th>
                            <th  rowspan="2">Inspected By</th>
                        </tr>
                        <tr class="text-center bg-light">
                            <th>M/C.</th>
                            <th>RM.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($firDimensionData)) :
                            $i = 1; $totalMcRejQty =0;$totalRMRejQty =0;$totalUDQkQty =0;
                            foreach ($firDimensionData as $row) :
                                $diamention = '';
                                if ($row->requirement == 1) { $diamention = $row->min_req . '/' .  $row->max_req; }
                                if ($row->requirement == 2) {  $diamention = $row->min_req . ' ' .  $row->other_req;  }
                                if ($row->requirement == 3) { $diamention = $row->max_req . ' ' .  $row->other_req;  }
                                if ($row->requirement == 4) { $diamention = $row->other_req;  }
                                $totalMcRejQty +=$row->mc_rej_qty;
                                $totalRMRejQty +=$row->rm_rej_qty;
                                $totalUDQkQty +=$row->ud_ok_qty;
                        ?>
                                <tr class="text-center">
                                    <td><?= $i ?></td>
                                    <td class="text-left"><?php if (!empty($row->char_class)) { ?><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/' . $row->char_class . '.png') ?>"><?php } ?></td>
                                    <td><?= $row->parameter ?></td>
                                    <td><?= $diamention ?></td>
                                    <td><?= $row->instrument_code ?></td>
                                    <td><?= $row->potential_cause ?></td>
                                    <td><?= !empty($row->ok_qty) ? floatval($row->ok_qty) : '' ?> </td>
                                    <td><?= !empty($row->ud_ok_qty) ? floatval($row->ud_ok_qty) : '' ?></td>
                                    <td><?= !empty($row->mc_rej_qty) ? floatval($row->mc_rej_qty) : '' ?></td>
                                    <td><?= !empty($row->rm_rej_qty) ? floatval($row->rm_rej_qty) : '' ?></td>
                                    <td><?= !empty($row->rw_qty) ? floatval($row->rw_qty) : '' ?></td>
                                    <td><?= !empty($row->emp_name) ? $row->emp_name : '' ?> </td>
                                </tr>

                            <?php $i++;
                            endforeach;
                        else : ?>
                            <tr>
                                <td colspan="12" class="text-center">No data available in table </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/critical.png') ?>"> <span style="">Critical Characteristic </span> </td>
                            <td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/major.png') ?>"> <span style="">Major </span></td>
                            <td><img style="width:25px;display:inline-block;vertical-align:middle;" src="<?= base_url('assets/images/symbols/minor.png') ?>"> <span style="">Minor</span></td>
                            <th class="text-left" > Total </th>
                            <th><?= (!empty(floatval($dataRow->total_ok_qty))) ? floatval($dataRow->total_ok_qty) : 0 ?></th>
                            <th><?= (!empty(floatval($totalUDQkQty))) ? floatval($totalUDQkQty) : 0 ?></th>
                            <th><?= (!empty(floatval($totalMcRejQty))) ? floatval($totalMcRejQty) : 0 ?></th>
                            <th><?= (!empty(floatval($totalRMRejQty))) ? floatval($totalRMRejQty) : 0 ?></th>
                            <th><?= (!empty(floatval($dataRow->total_rw_qty))) ? floatval($dataRow->total_rw_qty) : 0 ?></th>
                            <th></th>
                        </tr>
                        <tr>
							<td style="width:50%;" colspan="8"><b>Comment : </b><?= !empty($dataRow->remark) ? $dataRow->remark : '' ?></td>
							<td style="width:25%;" colspan="2" class="text-center"><b>Inspected By</b></td>
							<td style="width:25%;" colspan="2" class="text-center"><b>Verified By</b></td>
						</tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</form>