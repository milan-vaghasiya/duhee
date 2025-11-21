<!-- 2 -->
<div class="row">
    <div class="col-12">
        <?php
        $drg_no = (!empty($dataRow->drawing_no)? $dataRow->drawing_no : '');
        $rev_no = (!empty($dataRow->rev_no)? $dataRow->rev_no : '');
        ?>

        <table class="table item-list-bb">
            <tr>
                <td style="width:20%;"><img src="assets/images/logo.png" style="max-height:40px;"></td>
                <td class="org_title text-center" style="font-size:1.5rem;">ACCEPTANCE QUALITY REPORT<br>NPD / Regular Parts</td>
                <td style="width:20%;">Doc No : FMT-QA-13 <br> Rev No : <?=$rev_no?><br> Rev Date : <?=(!empty($dataRow->rev_date)? formatDate($dataRow->rev_date) : '')?></td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td><b>Part No : </b><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
                <td><b>Dispatch Qty : </b><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty).' NOS.' : '') ?></td>
                <td><b>Type : </b><?= (!empty($dataRow->type) ? $dataRow->type : '') ?></td>
            </tr>
            <tr>
                <td><b>Part Name : </b><?= (!empty($dataRow->item_name) ? $dataRow->item_name : '') ?></td>
                <td><b>Invoice No : </b><?= (!empty($dataRow->trans_number) ? $dataRow->trans_number : '') ?></td>
                <td><b>Operation No : </b><?= (!empty($dataRow->operation_no) ? $dataRow->operation_no : '') ?></td>
            </tr>
            <tr>
                <td><b>Drg. Rev. : </b><?= ((!empty($drg_no) && !empty($rev_no)) ? $drg_no .' & '. $rev_no : '') ?></td>
                <td><b>Heat Code : </b><?= (!empty($dataRow->heat_code) ? $dataRow->heat_code : '') ?></td>
                <td><b>M.R.R No & Date : </b><?= (!empty($dataRow->challan_no) ? $dataRow->challan_no.' & '.formatDate($dataRow->challan_date) : '') ?></td>
            </tr>
            <tr>
                <td><b>Customer /Supplier : </b><?= (!empty($companyData->company_name) ? strtoupper($companyData->company_name) : '') ?></td>
                <td><b>Supplier Inspection Date : </b><?= (!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '') ?></td>
                <td><b>Oerlikon Inspection Date : </b></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="2" style="width:5%">Sr. No</th>
                    <th rowspan="2" style="width:15%">Description Of Parameter</th>
                    <th rowspan="2" style="width:10%">Acceptance Criteria</th>
                    <th rowspan="2" style="width:10%">Mode of Inspection</th>
                    <th colspan="5">Supplier Observation</th>
                    <th style="width:7%">Page1of</th>
                    <th colspan="5">Graziano Observation</th>
                    <th style="width:7%">Page1of</th>
                </tr>
                <tr class="bg-light">
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>Remarks<br>OK<br>NG</th>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>Remarks<br>OK<br>NG</th>
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
                                    <td colspan="4" rowspan="4" style="vertical-align:top;"><b>Remarks : </b>' . (!empty($dataRow->master_remark) ? $dataRow->master_remark : '') . '</td>
                                    <td colspan="3" height="30" class="text-center">INSPECTION RESULTS</td>                                   
                                    <td colspan="2" rowspan="2" style="vertical-align:bottom;" class="text-center"><b>' . (!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '') . ' <br>INSPECTED BY</b></td>
                                    <td colspan="4" class="text-center">INSPECTION RESULTS</td>
                                    <td colspan="3" rowspan="2" style="vertical-align:bottom;" class="text-center">INSPECTED BY</td>
                                </tr>
                                <tr>
                                    <td colspan="2" height="30">CONFORMING</td>
                                    <td></td>
                                    <td colspan="3">CONFORMING</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" height="30">NON CONFORMING</td>
                                    <td></td>
                                    <td colspan="2" rowspan="2" style="vertical-align:bottom;" class="text-center"><b>' . (!empty($dataRow->approved_by) ? $dataRow->approved_by : '') . ' <br>APPROVED BY</b></td>
                                    <td colspan="3">NON CONFORMING</td>
                                    <td></td>
                                    <td colspan="3" rowspan="2" style="vertical-align:bottom;" class="text-center">APPROVED BY</td>
                                </tr>
                                <tr>
                                    <td colspan="2" height="30">ACCEPTED U/D</td>
                                    <td></td>
                                    <td colspan="3">ACCEPTED U/D</td>
                                    <td></td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>