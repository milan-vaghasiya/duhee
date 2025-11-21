<!-- 5 -->
<div class="row">
    <div class="col-12">
        <?php
        $challan_no = (!empty($dataRow->challan_no)? $dataRow->challan_no : '');
        $challan_date = (!empty($dataRow->challan_date)? formatDate($dataRow->challan_date) : '');
        ?>

        <table class="table item-list-bb">
            <tr>
                <td style="width:20%;"><img src="assets/images/logo.png" style="max-height:40px;"></td>
                <td class="org_title text-center" style="font-size:1.5rem;">Incoming Inspection Report</td>
                <td style="width:20%;"><b>Format No - QF/QA/02 <br> Rev No : <?= (!empty($dataRow->rev_no) ? $dataRow->rev_no : '') ?> <br> Rev Date : <?= (!empty($dataRow->rev_date) ? formatDate($dataRow->rev_date) : '') ?></b></td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td><b>Supplier Name</b></td>
                <td><?= (!empty($companyData->company_name) ? strtoupper($companyData->company_name) : '') ?></td>
                <td><b>Part Name</b></td>
                <td><?= (!empty($dataRow->item_name) ? $dataRow->item_name : '') ?></td>
                <td><b>Part No.</b></td>
                <td><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
            </tr>
            <tr>
                <td><b>Challan No. & date</b></td>
                <td><?= ((!empty($challan_no) && !empty($challan_date)) ? $challan_no .' & '. $challan_date : '') ?></td>
                <td><b>Ch. Qty.</b></td>
                <td><?= (!empty($dataRow->inv_qty) ? floatval($dataRow->inv_qty).' NOS.' : '') ?></td>
                <td><b>Insp. Qty</b></td>
                <td><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty).' NOS.' : '') ?></td>
            </tr>
            <tr>
                <td><b>Supplier Code</b></td>
                <td>DA/18/1005</td>
                <td><b>Heat/Batch Code</b></td>
                <td><?= (!empty($dataRow->heat_code) ? $dataRow->heat_code : '') ?></td>
                <td><b>Inspection Report No.& Date</b></td>
                <td><?= (!empty($dataRow->trans_number) ? $dataRow->trans_number .' & '. formatDate($dataRow->trans_date) : '') ?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">S.N.</th>
                    <th rowspan="2" style="width:10%">Parameter</th>
                    <th rowspan="2" style="width:15%">Specification</th>
                    <th rowspan="2" style="width:10%">Instrument Used</th>
                    <th colspan="6">Supplier Observation</th>
                    <th colspan="5">ITPL Observation</th>
                    <th rowspan="2" style="width:5%">Remark</th>
                </tr>
                <tr class="bg-light">
                    <th style="width:5%">1</th>
                    <th style="width:5%">2</th>
                    <th style="width:5%">3</th>
                    <th style="width:5%">4</th>
                    <th style="width:5%">5</th>
                    <th style="width:5%">Remark</th>
                    <th style="width:5%">1</th>
                    <th style="width:5%">2</th>
                    <th style="width:5%">3</th>
                    <th style="width:5%">4</th>
                    <th style="width:5%">5</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $tbodyData = "";
                if (!empty($dataRow->itemData)) :
                    $i = 1;
                    foreach ($dataRow->itemData as $row) :
                        $diamention = '';
                        if ($row->requirement == 1) {
                            $diamention = $row->min_req . '/' . $row->max_req;
                        }
                        if ($row->requirement == 2) {
                            $diamention = $row->min_req . ' ' . $row->other_req;
                        }
                        if ($row->requirement == 3) {
                            $diamention = $row->max_req . ' ' . $row->other_req;
                        }
                        if ($row->requirement == 4) {
                            $diamention = $row->other_req;
                        }
                        $tbodyData .= '<tr>
                                        <td class="text-center">' . $i++ . '</td>
                                        <td>' . $row->parameter . '</td>
                                        <td>' . $diamention . '</td>
                                        <td class="text-center">' . $row->instrument_code . '</td>
                                        <td class="text-center">' . $row->sample_1 . '</td>
                                        <td class="text-center">' . $row->sample_2 . '</td>
                                        <td class="text-center">' . $row->sample_3 . '</td>
                                        <td class="text-center">' . $row->sample_4 . '</td>
                                        <td class="text-center">' . $row->sample_5 . '</td>
                                        <td class="text-center">' . $row->remark . '</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                    endforeach;
                    $tbodyData .= '<tr>
                                    <td colspan="3"><b>Supplier Remark : </b>'.(!empty($dataRow->sub_contract_remark) ? $dataRow->sub_contract_remark : '').'</td>
                                    <td colspan="7"></td>
                                    <td colspan="6"><b>Remark : </b>'.(!empty($dataRow->master_remark) ? $dataRow->master_remark : '').'</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b>Surface Treatment : </b>'.(!empty($dataRow->surface_treat) ? $dataRow->surface_treat : '').'</td>
                                    <td colspan="7"></td>
                                    <td colspan="6"><b>Accepted Qty : </b>'.(!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty) : '').'</td>
                                </tr>
                                <tr>
                                    <td colspan="3" rowspan="2" style="vertical-align:bottom;" class="text-center"><b>'.(!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '').'<br>Inspected By(Name & Sign)</b></td>
                                    <td colspan="7" rowspan="2" style="vertical-align:bottom;" class="text-center"><b>'.(!empty($dataRow->approved_by) ? $dataRow->approved_by : '').'<br>Approved By(Name & Sign)</b></td>                                    
                                    <td colspan="6"><b>Reject Qty : </b>'.(!empty($dataRow->reject_qty) ? floatval($dataRow->reject_qty) : '').'</td>
                                </tr>
                                <tr>
                                    <td colspan="6"><b>Rework Qty : </b>'.(!empty($dataRow->rework_qty) ? floatval($dataRow->rework_qty) : '').'</td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>