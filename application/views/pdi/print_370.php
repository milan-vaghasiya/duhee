<!-- 6 -->
<div class="row">
    <div class="col-12">
        <?php
        $challan_no = (!empty($dataRow->challan_no)? $dataRow->challan_no : '');
        $challan_date = (!empty($dataRow->challan_date)? formatDate($dataRow->challan_date) : '');
        ?>

        <table class="table item-list-bb">
            <tr>
                <td class="org_title text-center" style="font-size:1.5rem;"> SUPPLIER PDI CUM  MAHINDRA GEARS RECEIVING INSPECTION REPORT</td>
                <td style="width:20%;"><img src="assets/images/logo.png" style="max-height:40px;"></td>
            </tr>
        </table>

        <table class="table item-list-bb" style="margin-top:5px;">
            <tr>
                <td style="width:50%"><b>PART NO : </b><?= (!empty($dataRow->part_no) ? $dataRow->part_no : '') ?></td>
                <td style="width:50%"><b>SUPPLIER NAME : </b><?= (!empty($companyData->company_name) ? $companyData->company_name : '') ?></td>
            </tr>
            <tr>
                <td><b>PART NAME : </b><?= (!empty($dataRow->item_name) ? $dataRow->item_name : '') ?></td>
                <td><b>INV. CHALLAN NO & DATE : </b><?= ((!empty($challan_no) && !empty($challan_date)) ? $challan_no .' / '. $challan_date : '') ?></td>
            </tr>
            <tr>
                <td><b>CONDITION / STAGE : </b><?= (!empty($dataRow->type) ? $dataRow->type : '') ?></td>
                <td><b>CUSTOMER NAME ( To be fill by MGears) : </b><?= (!empty($dataRow->party_name) ? $dataRow->party_name : '') ?></td>
            </tr>
            <tr>
                <td><b>JOB CODE NO : </b><?= (!empty($dataRow->job_no) ? $dataRow->job_no : '') ?></td>
                <td><b>HEAT NO. : </b><?= (!empty($dataRow->heat_code) ? $dataRow->heat_code : '') ?></td>
            </tr>
            <tr>
                <td><b>LOT QTY : </b><?= (!empty($dataRow->lot_qty) ? floatval($dataRow->lot_qty).' NOS' : '') ?></td>
                <td><b>DATE OF INSP. : </b><?= (!empty($dataRow->trans_date) ? formatDate($dataRow->trans_date) : '') ?></td>
            </tr>
        </table>
        
        <table class="table item-list-bb" style="margin-top:5px;">
            <thead>
                <tr class="bg-light">
                    <th rowspan="3" style="width:5%">Sr.no</th>
                    <th rowspan="3" style="width:10%">Product Characteristics</th>
                    <th rowspan="3" style="width:15%">PRODUCTS SPECIFICATION / TOLERANCE</th>
                    <th rowspan="3" style="width:10%">Instrument / Gauges</th>
                    <th colspan="5">Supplier</th>
                    <th colspan="5">Mahindra Gears</th>
                    <th rowspan="3" style="width:10%">Remarks</th>
                </tr>
                <tr class="bg-light">
                    <th colspan="5">Observed Dimensions</th>
                    <th colspan="5">Observed Dimensions</th>
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
                                        <td>' . $row->parameter . '</td>
                                        <td>' . $diamention . '</td>
                                        <td class="text-center">' . $row->instrument_code . '</td>
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
                                    <td colspan="4" rowspan="5" style="vertical-align:top;"><b>REMARKS/ ACTIONS RECOMMENDED : </b>'.(!empty($dataRow->master_remark) ? $dataRow->master_remark : '').'</td>
                                    <td colspan="5"><b>Inspected by : </b>'.(!empty($dataRow->inspected_by) ? $dataRow->inspected_by : '').'</td>
                                    <td colspan="6"><b>Inspected by : </b></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><b>Sign : </b></td>
                                    <td colspan="3"><b>Name : </b></td>
                                    <td colspan="3"><b>Sign : </b></td>
                                    <td colspan="3"><b>Name : </b></td>
                                </tr>
                                <tr>z
                                    <td colspan="5"><b>Date : </b></td>
                                    <td colspan="6"><b>Date : </b></td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>PDI Incharge (Name/ Sign) : </b>'.(!empty($dataRow->in_charge_name1) ? $dataRow->in_charge_name1 : '').'</td>
                                    <td colspan="6"><b>RQA Incharge : </b></td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>Status: Accepted /Under deviation</b></td>
                                    <td colspan="6"><b>Status: Accepted /Under deviation/ Rejected</b></td>
                                </tr>
                                <tr>
                                    <td colspan="15"><b>Format no.: F-QAL-01-03 Rev. No : '.(!empty($dataRow->rev_no) ? $dataRow->rev_no : '').' Eff. Date : '.(!empty($dataRow->rev_date) ? formatDate($dataRow->rev_date) : '').'</b></td>
                                </tr>';
                endif;
                echo $tbodyData;
                ?>
            </tbody>
        </table>

    </div>
</div>