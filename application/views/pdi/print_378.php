<!-- 3 -->
<div class="row">
    <div class="col-12">

        <table class="table item-list-bb">
            <tr>
                <td class="org_title text-center" style="font-size:1.5rem;">INSPECTION REPORT</td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td><b><?= (!empty($companyData->company_name)? strtoupper($companyData->company_name) : '') ?></b></td>
                <td><b>CUSTOMER : </b><?= (!empty($dataRow->party_name)? $dataRow->party_name : '') ?></td>
                <td><b>PART NAME : </b><?= (!empty($dataRow->item_name) ? $dataRow->item_name : '') ?></td>
            </tr>
            <tr>
                <td><b>REPORT NO. : </b><?= (!empty($dataRow->trans_number) ? $dataRow->trans_number : '') ?></td>
                <td><b>D.C.QUANTITY : </b><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty). ' NOS.' : '') ?></td>
                <td><b>PART NO. : </b><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
            </tr>
            <tr>
                <td><b>DATE : </b><?= (!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '') ?></td>
                <td><b>MAT. SPEC. : </b><?= (!empty($dataRow->material_grade) ? $dataRow->material_grade : '') ?></td>
                <td><b>PART REV.LEVEL & DATE : </b><?= (!empty($dataRow->rev_no) ? $dataRow->rev_no .' & '.formatDate($dataRow->rev_date) : '') ?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">S.N.</th>
                    <th rowspan="2" style="width:5%">SC</th>
                    <th rowspan="2" style="width:25%">SPECIFICATIONS</th>
                    <th colspan="5">OBSERVATIONS - DASP</th>
                    <th colspan="5">OBSERVATIONS - DWPL</th>
                    <th rowspan="2" style="width:10%">REMARKS</th>
                </tr>
                <tr class="bg-light">
                    <th style="width:5%">1</th>
                    <th style="width:5%">2</th>
                    <th style="width:5%">3</th>
                    <th style="width:5%">4</th>
                    <th style="width:5%">5</th>
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
                                        <td></td>
                                        <td>' . $diamention . '</td>
                                        <td class="text-center">' . $row->sample_1 . '</td>
                                        <td class="text-center">' . $row->sample_2 . '</td>
                                        <td class="text-center">' . $row->sample_3 . '</td>
                                        <td class="text-center">' . $row->sample_4 . '</td>
                                        <td class="text-center">' . $row->sample_5 . '</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-center">' . $row->remark . '</td>
                                    </tr>';
                    endforeach;
                    $tbodyData .= '<tr>
                                    <td colspan="5"><b>SUB-CONTRACTOR REMARK : </b>'.(!empty($dataRow->sub_contract_remark) ? $dataRow->sub_contract_remark : '').'</td>
                                    <td colspan="5"><b>VERIFICATION BY DASP : </b>'.(!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '').'</td>
                                    <td colspan="4"><b>VERIFICATION BY DWPL : </b>'.(!empty($dataRow->approved_by) ? $dataRow->approved_by : '').'</td>
                                </tr>
                                <tr>
                                    <td colspan="3"><b>D.C. No. : </b>'.(!empty($dataRow->po_no) ? $dataRow->po_no : '').'</td>
                                    <td colspan="2"><b>DATE : </b>'.(!empty($dataRow->po_date) ? formatDate($dataRow->po_date) : '').'</td>
                                    <td colspan="5"><b>GRR NO. : </b>'.(!empty($dataRow->challan_no) ? $dataRow->challan_no : '').'</td>
                                    <td colspan="4"><b>DATE : </b>'.(!empty($dataRow->challan_date) ? formatDate($dataRow->challan_date) : '').'</td>
                                </tr>
                                <tr>
                                    <td colspan="5" rowspan="2"><b>REMARKS : </b>'.(!empty($dataRow->master_remark) ? $dataRow->master_remark : '').'</td>
                                    <th colspan="2"><b>REMARKS</b></th>
                                    <th colspan="2"><b>ACCEPTED</b></th>
                                    <th colspan="2"><b>REJECTED</b></th>
                                    <th colspan="2"><b>REWORK</b></th>
                                    <th><b>CONDITIONALLY ACCEPTED</b></th>
                                </tr>
                                <tr>
                                    <th colspan="2"><b>QUANTITY</b></th>
                                    <td colspan="2" class="text-center">'.(!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty) : '').'</td>
                                    <td colspan="2" class="text-center">'.(!empty($dataRow->reject_qty) ? floatval($dataRow->reject_qty) : '').'</td>
                                    <td colspan="2" class="text-center">'.(!empty($dataRow->rework_qty) ? floatval($dataRow->rework_qty) : '').'</td>
                                    <td class="text-center">'.(!empty($dataRow->condition_accept_qty) ? floatval($dataRow->condition_accept_qty) : '').'</td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

        <table class="item-list-bb">
            <tr>
                <td class="text-center" style="width:16.5%"><?= (!empty($dataRow->tech_date1) ? formatDate($dataRow->tech_date1) : '') ?></td>
                <td class="text-center" style="width:16%"><?= (!empty($dataRow->tech_name1) ? $dataRow->tech_name1 : '') ?></td>
                <td class="text-center" style="width:16%"><?= (!empty($dataRow->in_charge_name1) ? $dataRow->in_charge_name1 : '') ?></td>
                <td class="text-center" style="width:10%"><?= (!empty($dataRow->tech_date2) ? formatDate($dataRow->tech_date2) : '') ?></td>
                <td class="text-center" style="width:10%"><?= (!empty($dataRow->tech_name2) ? $dataRow->tech_name2 : '') ?></td>
                <td class="text-center"><?= (!empty($dataRow->in_charge_name2) ? $dataRow->in_charge_name2 : '') ?></td>
                <td><b>SAMPLE QTY. : </b><?= (!empty($dataRow->sample_qty) ? floatval($dataRow->sample_qty) : '') ?> Nos.</td>
            </tr>
            <tr>
                <th>DATE</th>
                <th>TECHNICIAN</th>
                <th>IN CHARGE</th>
                <th>DATE</th>
                <th>TECHNICIAN</th>
                <th>IN CHARGE</th>
                <td><b>VERIFIED QTY. : </b><?= (!empty($dataRow->verify_qty) ? floatval($dataRow->verify_qty) : '') ?> Nos.</td>
            </tr>
        </table>

    </div>
</div>